<?php

namespace System\Classes\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use System\Models\Parameter;
use System\Traits\InteractsWithZip;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Network\Http as NetworkHttp;
use Winter\Storm\Packager\Composer;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Http;
use Exception;
use Winter\Storm\Support\Facades\Url;
use Winter\Storm\Support\Traits\Singleton;

/**
 * @class MarketPlaceApi
 * @method static array search(string $query, string $productType = '')
 */
class MarketPlaceApi
{
    use Singleton;
    use InteractsWithZip;

    public const PRODUCT_CACHE_KEY = 'system-updates-product-details';
    public const PRODUCT_FETCH_CACHE_KEY = 'marketplace.api.products';

    public const REQUEST_PLUGIN_DETAIL = 'plugin/detail';
    public const REQUEST_PLUGIN_CONTENT = 'plugin/content';
    public const REQUEST_THEME_DETAIL = 'theme/detail';
    public const REQUEST_PROJECT_DETAIL = 'project/detail';

    /**
     * Secure API Key
     */
    protected ?string $key = null;

    /**
     * Secure API Secret
     */
    protected ?string $secret = null;

    /**
     * @var string Used during download of files
     */
    protected string $tempDirectory;

    /**
     * @var string Directs the UpdateManager where to unpack archives to
     */
    protected string $baseDirectory;

    /**
     * Cache of gateway products
     */
    protected array $productCache = [
        'theme' => [],
        'plugin' => [],
    ];


    public function init()
    {
        if (Cache::has(static::PRODUCT_CACHE_KEY)) {
            $this->productCache = Cache::get(static::PRODUCT_CACHE_KEY);
        }

        $this->setTempDirectory(temp_path())
            ->setBaseDirectory(base_path());
    }

    /**
     * Set the API security for all transmissions.
     */
    public function setSecurity(string $key, string $secret): void
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Set the temp directory used by the UpdateManager. Defaults to `temp_path()` but can be overwritten if required.
     *
     * @param string $tempDirectory
     * @return $this
     */
    public function setTempDirectory(string $tempDirectory): static
    {
        $this->tempDirectory = $tempDirectory;

        // Ensure temp directory exists
        if (!File::isDirectory($this->tempDirectory) && File::isWritable($this->tempDirectory)) {
            File::makeDirectory($this->tempDirectory, recursive: true);
        }

        return $this;
    }

    /**
     * Set the base directory used by the UpdateManager. Defaults to `base_path()` but can be overwritten if required.
     *
     * @param string $baseDirectory
     * @return $this
     */
    public function setBaseDirectory(string $baseDirectory): static
    {
        $this->baseDirectory = $baseDirectory;

        // Ensure temp directory exists
        if (!File::isDirectory($this->baseDirectory)) {
            throw new \RuntimeException('The base directory "' . $this->baseDirectory . '" does not exist.');
        }

        return $this;
    }

    /**
     * Calculates a file path for a file code
     */
    protected function getFilePath(string $fileCode): string
    {
        return $this->tempDirectory . '/' . md5($fileCode) . '.arc';
    }

    /**
     * Handles fetching data for system info stuff maybe
     *
     * @param string $request
     * @param string $identifier
     * @return array
     * @throws ApplicationException
     */
    public function request(string $request, string $identifier): array
    {
        if (
            !in_array($request, [
                static::REQUEST_PLUGIN_CONTENT,
                static::REQUEST_PLUGIN_DETAIL,
                static::REQUEST_THEME_DETAIL,
                static::REQUEST_PROJECT_DETAIL
            ])
        ) {
            throw new ApplicationException('Invalid request option.');
        }

        return $this->fetch(
            $request,
            [$request === static::REQUEST_PROJECT_DETAIL ? 'id' : 'name' => $identifier]
        );
    }

    /**
     * Contacts the update server for a response.
     * @throws ApplicationException
     */
    public function fetch(string $uri, array $postData = []): array
    {
        $result = Http::post($this->createServerUrl($uri), function ($http) use ($postData) {
            $this->applyHttpAttributes($http, $postData);
        });

        // @TODO: Refactor when marketplace API finalized
        if ($result->body === 'Package not found') {
            $result->code = 500;
        }

        if ($result->code == 404) {
            throw new ApplicationException(Lang::get('system::lang.server.response_not_found'));
        }

        if ($result->code != 200) {
            throw new ApplicationException(
                strlen($result->body)
                    ? $result->body
                    : Lang::get('system::lang.server.response_empty')
            );
        }

        try {
            $resultData = json_decode($result->body, true, flags: JSON_THROW_ON_ERROR);
        } catch (Exception $ex) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        if ($resultData === false || (is_string($resultData) && !strlen($resultData))) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        if (!is_array($resultData)) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        return $resultData;
    }

    /**
     * Downloads a file from the update server.
     * @param $uri - Gateway API URI
     * @param $fileCode - A unique code for saving the file.
     * @param $expectedHash - The expected file hash of the file.
     * @param $postData - Extra post data
     * @throws ApplicationException
     */
    public function fetchFile(string $uri, string $fileCode, string $expectedHash, array $postData = []): void
    {
        $filePath = $this->getFilePath($fileCode);

        $result = Http::post($this->createServerUrl($uri), function ($http) use ($postData, $filePath) {
            $this->applyHttpAttributes($http, $postData);
            $http->toFile($filePath);
        });

        if ($result->code != 200) {
            throw new ApplicationException(File::get($filePath));
        }

        if (md5_file($filePath) != $expectedHash) {
            @unlink($filePath);
            throw new ApplicationException(Lang::get('system::lang.server.file_corrupt'));
        }
    }

    /**
     * @param string $query The query to search for
     * @param string $productType Either "plugin" or "theme"
     * @return array
     * @throws ApplicationException
     */
    public function search(string $query, string $productType = ''): array
    {
        $serverUri = $productType === 'plugin' ? 'plugin/search' : 'theme/search';

        return $this->fetch($serverUri, ['query' => $query]);
    }

    public function requestProductDetails(array|string $codes, string $type = null): array
    {
        if (!in_array($type, ['plugin', 'theme'])) {
            $type = 'plugin';
        }

        $codes = is_array($codes) ? $codes : [$codes];

        /*
         * New products requested
         */
        $productCodesNotInCache = array_diff($codes, array_keys($this->productCache[$type]));
        if (count($productCodesNotInCache)) {
            $data = $this->fetchProducts(
                $type,
                '/details',
                'system-updates-products-' . crc32(implode(',', $productCodesNotInCache)),
                ['names' => $productCodesNotInCache]
            );

            /*
             * Cache unknown products
             */
            $unknownCodes = array_diff(
                $productCodesNotInCache,
                array_map(fn ($product) => array_get($product, 'code', -1), $data)
            );

            foreach ($unknownCodes as $code) {
                $this->cacheProductDetail($type, $code, -1);
            }

            $this->saveProductCache();
        }

        /*
         * Build details from cache
         */
        $result = [];
        $requestedDetails = array_intersect_key($this->productCache[$type], array_flip($codes));

        foreach ($requestedDetails as $detail) {
            if ($detail === -1) {
                continue;
            }
            $result[] = $detail;
        }

        return $result;
    }

    /**
     * Returns popular themes found on the marketplace.
     */
    public function requestPopularProducts(string $type = null): array
    {
        if (!in_array($type, ['plugin', 'theme'])) {
            $type = 'plugin';
        }

        return $this->fetchProducts($type, '/popular', 'system-updates-popular-' . $type);
    }

    public function fetchProducts(string $type, string $url, string $cacheKey, array $postData = []): array
    {
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data = $this->fetch($type . $url);

        Cache::put($cacheKey, $data, now()->addMinutes(60));

        foreach ($data as $product) {
            $code = array_get($product, 'code', -1);
            $this->cacheProductDetail($type, $code, $product);
        }

        $this->saveProductCache();

        return $data;
    }

    /**
     * Returns the latest changelog information.
     */
    public function requestChangelog(): array
    {
        $build = Parameter::get('system::core.build');

        // Determine branch
        if (!is_null($build)) {
            $branch = explode('.', $build);
            array_pop($branch);
            $branch = implode('.', $branch);
        }

        $result = Http::get($this->createServerUrl('changelog' . ((!is_null($branch)) ? '/' . $branch : '')));

        if ($result->code == 404) {
            throw new ApplicationException(Lang::get('system::lang.server.response_empty'));
        }

        if ($result->code != 200) {
            throw new ApplicationException(
                strlen($result->body)
                    ? $result->body
                    : Lang::get('system::lang.server.response_empty')
            );
        }

        try {
            $resultData = json_decode($result->body, true);
        } catch (Exception $ex) {
            throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
        }

        return $resultData;
    }

    /**
     * Create a nonce based on millisecond time
     */
    protected function createNonce(): int
    {
        $mt = explode(' ', microtime());
        return $mt[1] . substr($mt[0], 2, 6);
    }

    /**
     * Create a unique signature for transmission.
     */
    protected function createSignature(array $data, string $secret): string
    {
        return base64_encode(hash_hmac('sha512', http_build_query($data, '', '&'), base64_decode($secret), true));
    }

    /**
     * Create a complete gateway server URL from supplied URI
     */
    protected function createServerUrl(string $uri): string
    {
        $gateway = Config::get('cms.updateServer', 'https://api.wintercms.com/marketplace');

        if (!str_ends_with($gateway, '/')) {
            $gateway .= '/';
        }

        return $gateway . $uri;
    }

    protected function cacheProductDetail(string $type, string $code, array|int $data): void
    {
        $this->productCache[$type][$code] = $data;
    }

    protected function saveProductCache(): void
    {
        $expiresAt = Carbon::now()->addDays(2);
        Cache::put(static::PRODUCT_CACHE_KEY, $this->productCache, $expiresAt);
    }

    /**
     * Modifies the Network HTTP object with common attributes.
     */
    protected function applyHttpAttributes(NetworkHttp $http, array $postData): void
    {
        $postData['protocol_version'] = '1.1';
        $postData['client'] = 'october';

        $postData['server'] = base64_encode(serialize([
            'php'   => PHP_VERSION,
            'url'   => Url::to('/'),
            'since' => Parameter::get('system::app.birthday'),
        ]));

        if ($projectId = Parameter::get('system::project.id')) {
            $postData['project'] = $projectId;
        }

        if (Config::get('cms.edgeUpdates', false)) {
            $postData['edge'] = 1;
        }

        if ($this->key && $this->secret) {
            $postData['nonce'] = $this->createNonce();
            $http->header('Rest-Key', $this->key);
            $http->header('Rest-Sign', $this->createSignature($postData, $this->secret));
        }

        if ($credentials = Config::get('cms.updateAuth')) {
            $http->auth($credentials);
        }

        $http->noRedirect();
        $http->data($postData);
    }

    /**
     * Downloads a theme from the update server.
     */
    public function downloadTheme(string $name, string $hash): static
    {
        $fileCode = $name . $hash;
        $this->fetchFile('theme/get', $fileCode, $hash, ['name' => $name]);
        return $this;
    }

    /**
     * Extracts a theme after it has been downloaded.
     * @throws ApplicationException
     */
    public function extractTheme(string $name, string $hash): void
    {
        $fileCode = $name . $hash;
        $filePath = $this->getFilePath($fileCode);

        $this->extractArchive($filePath, themes_path());
    }

    /**
     * Looks up a plugin from the update server.
     */
    public function requestPluginDetails(string $name): array
    {
        return $this->api->fetch('plugin/detail', ['name' => $name]);
    }

    /**
     * Downloads a plugin from the update server.
     * @param bool $installation Indicates whether this is a plugin installation request.
     */
    public function downloadPlugin(string $name, string $hash, bool $installation = false): static
    {
        $fileCode = $name . $hash;
        $this->fetchFile('plugin/get', $fileCode, $hash, [
            'name'         => $name,
            'installation' => $installation ? 1 : 0
        ]);
        return $this;
    }

    /**
     * Extracts a plugin after it has been downloaded.
     */
    public function extractPlugin(string $name, string $hash): void
    {
        $fileCode = $name . $hash;
        $filePath = $this->getFilePath($fileCode);

        $this->extractArchive($filePath, plugins_path());
    }


    ////////////////////////////////////////////////
    /// @TODO: Move this to the marketplace api
    ////////////////////////////////////////////////

    public function getPackagesFromBazaar(): array
    {
        // @TODO: Remove, this allows for demo without public api
        if (file_exists(__DIR__ . '/package-list.json') && !env('UPDATE_SERVER')) {
            return json_decode(file_get_contents(__DIR__ . '/package-list.json'), JSON_OBJECT_AS_ARRAY);
        }

        $result = Http::get($this->createServerUrl('v1/packages/list'));

        if ($result->code !== 200) {
            throw new ApplicationException('Bad things have happened');
        }

        return json_decode($result->body, JSON_OBJECT_AS_ARRAY);
    }

    public function getListing(): array
    {

//        $packages = Cache::remember(
//            static::PRODUCT_FETCH_CACHE_KEY,
//            Carbon::now()->addMinutes(15),
//            fn () => $this->getPackagesFromBazaar()
//        );
        // @TODO: Caching
        $packages = $this->getPackagesFromBazaar();

        $installed = Composer::getWinterPackagesWithVersion();

        foreach ($packages['products'] as $type => $products) {
            $packages['products'][$type] = array_map(function (array $package) use ($installed) {
                $package['installed'] = isset($installed[$package['composer_package']]);
                $package['installed_version'] = $package['installed']
                    ? $installed[$package['composer_package']]['version']
                    : null;
                $package['installed_ref'] = $package['installed']
                    ? $installed[$package['composer_package']]['ref']
                    : null;
                return $package;
            }, $products);
        }

        return $packages;
    }

    /**
     * @TODO: This whole function should be provided by the marketplace api
     * @param string $type
     * @return array
     */
    protected function getPackageType(string $type): array
    {
        $packages = Composer::listPackages($type);

        usort($packages, function ($a, $b) {
            return $b['favers'] <=> $a['favers'];
        });

        $popular = array_slice($packages, 0, 9);

        usort($packages, function ($a, $b) {
            return str_starts_with($b['name'], 'winter/');
        });

        $featured = array_slice($packages, 0, 9);

        usort($packages, function ($a, $b) {
            return $b['downloads'] <=> $a['downloads'];
        });

        return [
            'popular' => $popular,
            'featured' => $featured,
            'all' => $packages
        ];
    }
}
