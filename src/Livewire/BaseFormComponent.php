<?php

namespace CrudBooster\Livewire;

use CrudBooster\Attributes\WithAttributeCaller;
use CrudBooster\Components\AlertMessage\WithAlertMessage;
use CrudBooster\Components\ConfirmMessage\WithConfirmMessage;
use CrudBooster\Components\MasterDetail\WithMasterDetail;
use CrudBooster\Components\Type\WithTypeCommon;
use CrudBooster\Livewire\Delete\WithDelete;
use CrudBooster\Livewire\FormBuilder\WithFormBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseFormComponent extends BaseModuleAbstract
{
    use withAlertMessage;
    use withDelete;
    use withFormBuilder;
    use WithTypeCommon;
    use WithAttributeCaller;
    use WithConfirmMessage;
    use WithMasterDetail;

    public $__view;
    protected $viewOverride;
    protected string $viewForm = 'cb.themes::form';
    protected string $viewDetail = 'cb.themes::detail';
    public ?string $formId = null;
    public array $formData = [];
    private array $formValidation = [];
    private array $formValidationMessage = [];
    public array $ignoreSaveOnEmpty = [];
    public array $ignoreSave = [];
    public $saveMode = 'save'; // Either save or saveAndMore
    public $buttonDelete = true;
    public $buttonEdit = true;
    public $formDialog = false;
    public $foreignKey = null;
    public $foreignKeyFilter = null;
    public $redirectDetailOnSave = false;
    
    // ADD THESE PROPERTIES
    // Store ref and parent-module parameters from initial request
    public $refParameter = null;
    public $parentModuleParameter = null;
    public $ref = null; // For template access
    public $currentUrl = null; // Store current URL for template access

    /**
     * This function is like constructor
     * @param null $actionOne
     * @param null $actionTwo
     * @param null $moduleKey
     * @param bool $formDialog
     * @param null $foreignKey
     * @param null $foreignKeyValue
     * @return void
     */
    public function mount($actionOne = null, $actionTwo = null, $moduleKey = null, $formDialog = false, $foreignKey = null, $foreignKeyValue = null): void
    {
        $this->moduleKey = $moduleKey;
        $this->formDialog = $formDialog;
        $this->foreignKey = $foreignKey;
        $this->foreignKeyFilter = $foreignKeyValue;        
        $this->refParameter = request('ref');
        $this->parentModuleParameter = request('parent-module');
        $this->ref = request('ref'); // For template access
        $this->currentUrl = url()->full(); // Store current URL for template access
        
        // Handle encrypted parent-module parameter
        $this->handleEncryptedParentModule();

        // Re-show a flashed alert (e.g. the "created successfully" message from
        // Save & Add More, which redirects back here instead of to a browse page).
        $this->__alertMessageBrowseMounting();

        // Call anything attribute OnFormMounting
        $this->callOnFormMounting($this->modelName);
        // Check authorization
        $this->checkAuthorization($actionOne, $actionTwo);
        // Set ignore config
        $this->ignoreSaveOnEmpty = config('cb.ignore_save_on_empty', $this->ignoreSaveOnEmpty);
        $this->ignoreSave = config('cb.ignore_save', $this->ignoreSave);
        // Routing view
        $this->routingView($actionOne, $actionTwo);
        // Determine UUID and set to formUuid
        $this->determinteId($actionOne, $actionTwo);        
        // First init, since some attribute need to be set before the form data is populated
        $this->init();
        // Populate form data
        $this->getData($this->formId);
        // Check if form data is empty on edit / detail page
        $this->dataEmptyValidator($this->formId);        
        // Second Init, since some attribute need to be set after the form data is populated
        $this->__formInit();
        // Call anything attribute OnFormMounted
        $this->callOnFormMounted($this->modelName);
    }

    /**
     * Handle encrypted parent-module parameter for security
     */
    private function handleEncryptedParentModule(): void
    {
        if ($this->parentModuleParameter && !$this->foreignKey) {
            try {
                $parentData = json_decode(decrypt($this->parentModuleParameter), true);
                
                if (isset($parentData['parent_id'], $parentData['foreign_key'])) {
                    $this->foreignKey = $parentData['foreign_key'];
                    $this->foreignKeyFilter = $parentData['parent_id'];
                }
            } catch (\Exception $e) {
                // Invalid encrypted data, ignore silently for security
                \Log::warning('Invalid parent-module parameter: ' . $e->getMessage());
            }
        }
    }

    private function checkAuthorization($actionOne, $actionTwo): void
    {
        if(!auth()->user()->can('read', $this->module['key'])) {
            $this->showAlertMessage('You are not authorized to access this page', 'warning');
            $backUrl = $this->refParameter ? urldecode($this->refParameter) : getCmsUrl($this->redirectBackPath); // CHANGED
            $this->redirectIntended($backUrl, navigate: true);
        }
        if($actionOne == 'create' && !auth()->user()->can('create', $this->module['key'])) {
            $this->showAlertMessage('You are not authorized to access this page', 'warning');
            $backUrl = $this->refParameter ? urldecode($this->refParameter) : getCmsUrl($this->redirectBackPath); // CHANGED
            $this->redirect($backUrl, navigate: true);
        } else if($actionTwo == 'edit' && !auth()->user()->can('update', $this->module['key'])) {
            $this->showAlertMessage('You are not authorized to access this page', 'warning');
            $backUrl = $this->refParameter ? urldecode($this->refParameter) : getCmsUrl($this->redirectBackPath); // CHANGED
            $this->redirect($backUrl, navigate: true);
        }
    }

    private function determinteId($actionOne, $actionTwo)
    {
        $this->formId = $actionOne !== 'create' ? $actionOne : null;
    }

    private function routingView($actionOne, $actionTwo)
    {
        $this->__view = $actionOne == 'create' || $actionTwo == 'edit' ? $this->viewForm : $this->viewDetail;
        $this->__view = $this->viewOverride ?? $this->__view;
    }

    /**
     * This function is used to validate data empty
     * @param $id
     * @return void
     */
    private function dataEmptyValidator($id): void
    {
        if (!$this->formData && $id) {
            $this->showAlertMessage('Data not found', 'warning');
            $backUrl = $this->refParameter ? urldecode($this->refParameter) : getCmsUrl($this->redirectBackPath); // CHANGED
            $this->redirect($backUrl, navigate: true);
        }
    }

    /**
     * This function is used to get data by uuid and set the form data
     * @param $formId
     * @return void
     */
    private function getData($formId = null): void
    {
        $this->callOnFormGettingData($this->modelName, $formId);
        if (!$formId) {
            return;
        }
        $data = $this->modelService::find($formId);
        $this->formData = $data ? $data->toArray() : [];
        $this->callOnFormGetData($this->modelName, $this->formData, $formId);
    }


    /**
     * This function is used to save the form data, and called by livewire form submit
     * @return void
     */
    public function formSave(): void
    {
        try {
            $this->callOnFormSaving($this->modelName, $this->formData, $this->formId);
        } catch (\Exception $e) {
            $this->showAlertMessage($e->getMessage(), 'warning');
            return;
        }

        // Construct all validation from columns
        $this->constructValidation();
        
        // Modify validation rules for file fields that have existing files
        if (method_exists($this, 'modifyFileValidationRules')) {
            $this->formValidation = $this->modifyFileValidationRules($this->formValidation);
        }
        
        if ($this->formValidation) {
            $this->validate($this->formValidation, $this->formValidationMessage);
        }

        if(config('cb.demo_mode')) {
            $this->showAlertMessage('This feature is disabled in demo mode', 'danger');
            $browsePath = $this->browsePath ?? 'dashboard';
            $backUrl = $this->refParameter ? urldecode($this->refParameter) : getCmsUrl($browsePath);
            $this->redirectIntended($backUrl, navigate: true);
            return;
        }

        // Call anything attribute OnFormValidated
        try {
            $this->callOnFormValidated($this->modelName, $this->formData, $this->formId);
        } catch (\Exception $e) {
            $this->showAlertMessage($e->getMessage(), 'warning');
            return;
        }

        try {
            DB::beginTransaction();

            $model = new $this->modelName();
            if ($this->formId) {
                $model = $this->modelService::find($this->formId);
            }

            // Assign form data to model object
            foreach ($this->formData as $key => $value) {
                $model->{$key} = $value;
            }

            // Ignore save if value is empty
            foreach ($this->ignoreSaveOnEmpty as $key) {
                if (empty($model->{$key})) {
                    unset($model->{$key});
                }
            }

            // Ignore save for fields
            foreach ($this->ignoreSave as $key) {
                unset($model->{$key});
            }

            $model->save();

            // This dispatch is used for user event
            $this->callOnFormSaved($this->modelName, $this->formData, $model->{$this->modelService::getPrimaryKey()});

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            $this->showAlertMessage("Oops something went wrong, please try again!", 'danger');
            return;
        }

        // Show success message
        $this->showAlertMessage($this->formId ? $this->pageTitle . ' has been updated successfully' : $this->pageTitle . ' has been created successfully');
        


        // Next step after save
        if($this->saveMode == 'save') {
            if(!$this->formDialog) {
                // REPLACE WITH THIS LOGIC:
                // Prioritize ref parameter over redirectDetailOnSave
                if($this->refParameter) {
                    // Use ref parameter if available
                    $backUrl = urldecode($this->refParameter);
                    $this->redirect($backUrl, navigate: true);
                } else if($this->redirectDetailOnSave) {
                    // Fallback to detail page if no ref
                    $this->redirect(getCmsUrl($this->module['mainPath'] . '/' . $model->{$this->modelService::getPrimaryKey()}), navigate: true);
                } else {
                    // Final fallback to redirectBackPath
                    $this->redirect(getCmsUrl($this->redirectBackPath), navigate: true);
                }
            } else {
                $this->dispatch('closeFormDialog');
            }
        } else {
            if(!$this->formDialog) {
                // REPLACE saveAndMore logic:
                // For saveAndMore mode, always go to create page, but include ref and parent-module parameters if exist
                $createUrl = getCmsUrl($this->module['mainPath'] . '/create');
                $queryParams = [];
                
                if($this->refParameter) {
                    $queryParams['ref'] = $this->refParameter;
                }
                
                if($this->parentModuleParameter) {
                    $queryParams['parent-module'] = $this->parentModuleParameter;
                }
                
                if(!empty($queryParams)) {
                    $createUrl .= '?' . http_build_query($queryParams);
                }
                
                $this->redirect($createUrl, navigate: true);
            } else {
                // Clear form data for next input
                $this->formData = [];
            }
        }
    }



    /**
     * This function is used to render livewire component
     * @return mixed
     */
    public function render(): mixed
    {
        $this->callOnFormRendering();
        return view($this->__view, [
            'pageTitle' => $this->pageTitle,
        ])->layout($this->layout)->title($this->pageTitle);
    }
}
