<?php

namespace System\Classes;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Backend\Facades\BackendAuth;

class SignedStorageUrlController extends Controller
{
    /**
     * Create a new signed URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->ensureEnvironmentVariablesAreAvailable($request);

        // @TODO: permissions checking
//        if (!BackendAuth::getUser()->hasPermission('system.stream_s3_uploads')) {
//            abort(403);
//        }

        $bucket = $request->input('bucket') ?: $_ENV['AWS_BUCKET'];

        $client = $this->storageClient();

        $uuid = (string) Str::uuid();

        $expiresAfter = config('vapor.signed_storage_url_expires_after', 5);

        $signedRequest = $client->createPresignedRequest(
            $this->createCommand($request, $client, $bucket, $key = ('tmp/'.$uuid)),
            sprintf('+%s minutes', $expiresAfter)
        );

        $uri = $signedRequest->getUri();

        return response()->json([
            'uuid' => $uuid,
            'bucket' => $bucket,
            'key' => $key,
            'url' => $uri->getScheme().'://'.$uri->getAuthority().$uri->getPath().'?'.$uri->getQuery(),
            'headers' => $this->headers($request, $signedRequest),
        ], 201);
    }

    /**
     * Create a command for the PUT operation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Aws\S3\S3Client  $client
     * @param  string  $bucket
     * @param  string  $key
     * @return \Aws\Command
     */
    protected function createCommand(Request $request, S3Client $client, $bucket, $key)
    {
        return $client->getCommand('putObject', array_filter([
            'Bucket' => $bucket,
            'Key' => $key,
            'ACL' => $request->input('visibility') ?: $this->defaultVisibility(),
            'ContentType' => $request->input('content_type') ?: 'application/octet-stream',
            'CacheControl' => $request->input('cache_control') ?: null,
            'Expires' => $request->input('expires') ?: null,
        ]));
    }

    /**
     * Get the headers that should be used when making the signed request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \GuzzleHttp\Psr7\Request
     * @return array
     */
    protected function headers(Request $request, $signedRequest)
    {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $request->input('content_type') ?: 'application/octet-stream',
            ]
        );
    }

    /**
     * Ensure the required environment variables are available.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function ensureEnvironmentVariablesAreAvailable(Request $request)
    {
        $missing = array_diff_key(array_flip(array_filter([
            $request->input('bucket') ? null : 'AWS_BUCKET',
            'AWS_DEFAULT_REGION',
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
        ])), $_ENV);

        if (empty($missing)) {
            return;
        }

        throw new InvalidArgumentException(
            'Unable to issue signed URL. Missing environment variables: '.implode(', ', array_keys($missing))
        );
    }

    /**
     * Get the S3 storage client instance.
     *
     * @return \Aws\S3\S3Client
     */
    protected function storageClient()
    {
        $config = [
            'region' => config('filesystems.disks.s3.region', $_ENV['AWS_DEFAULT_REGION']),
            'version' => 'latest',
            'signature_version' => 'v4',
            'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint', false),
        ];

        if (! isset($_ENV['AWS_LAMBDA_FUNCTION_VERSION'])) {
            $config['credentials'] = array_filter([
                'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
                'token' => $_ENV['AWS_SESSION_TOKEN'] ?? null,
            ]);

            if (
                !\Config::get('cms.streamS3Uploads.ignoreAwsUrl')
                && array_key_exists('AWS_URL', $_ENV)
                && !is_null($_ENV['AWS_URL'])
            ) {
                $config['url'] = $_ENV['AWS_URL'];
                $config['endpoint'] = $_ENV['AWS_URL'];
            }
        }

        return new S3Client($config);
    }

    /**
     * Get the default visibility for uploads.
     *
     * @return string
     */
    protected function defaultVisibility()
    {
        return 'private';
    }
}
