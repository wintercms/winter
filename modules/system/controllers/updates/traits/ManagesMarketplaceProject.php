<?php

namespace System\Controllers\Updates\Traits;

use Backend\Facades\Backend;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Winter\Storm\Exception\ApplicationException;
use System\Classes\Core\MarketPlaceApi;
use System\Models\Parameter;
use Winter\Storm\Support\Facades\Flash;

trait ManagesMarketplaceProject
{
    /**
     * Displays the form for entering a Project ID
     */
    public function onLoadProjectForm(): string
    {
        return $this->makePartial('project_form');
    }

    /**
     * Validate the project ID and execute the project installation
     */
    public function onAttachProject(): string
    {
        try {
            if (!$projectId = trim(post('project_id'))) {
                throw new ApplicationException(Lang::get('system::lang.project.id.missing'));
            }

            $result = MarketPlaceApi::instance()->request(MarketPlaceApi::REQUEST_PROJECT_DETAIL, $projectId);

            Parameter::set([
                'system::project.id'    => $projectId,
                'system::project.name'  => $result['name'],
                'system::project.owner' => $result['owner'],
            ]);

            return $this->onForceUpdate(false);
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('project_form');
        }
    }

    public function onDetachProject(): RedirectResponse
    {
        Parameter::set([
            'system::project.id'    => null,
            'system::project.name'  => null,
            'system::project.owner' => null,
        ]);

        Flash::success(Lang::get('system::lang.project.unbind_success'));
        return Backend::redirect('system/updates');
    }
}
