<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\Clients\ClientDetail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


/**
 * Class RolesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ClientsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel(\App\Models\Clients\Client::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/clients');
        $this->crud->setEntityNameStrings('Client', 'Clients');
    }

    protected function setupListOperation()
    {
        $name = [
            'name' => 'name', // the db column name (attribute name)
            'label' => "Client Name", // the human-readable label for it
            'type' => 'text' // the kind of column to show
        ];

        $active = [
            'name' => 'active', // the db column name (attribute name)
            'label' => "Active", // the human-readable label for it
            'type' => 'boolean' // the kind of column to show
        ];

        $active_edit = [
            'name' => 'active', // the db column name (attribute name)
            'label' => "Active", // the human-readable label for it
            'type' => 'checkbox' // the kind of column to show
        ];

        $column_defs = [$name, $active];
        CRUD::addColumns($column_defs);

        //$edit_create_defs = [$name, $active_edit];
        //CRUD::addFields($edit_create_defs, 'both');

    }

    protected function setupCreateOperation()
    {
        $this->crud->addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Client Name',
        ]);

        $this->crud->addField([
            'name' => 'active',
            'type' => 'boolean',
            'label' => 'Active',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $client_id = $this->crud->getCurrentEntryId();
        $client = $this->crud->getModel()->find($client_id);

        $website1_record = $client->website1_url()->first();
        $this->crud->addField([
            'name' => 'details[website1_url]',
            'type' => 'text',
            'label' => 'Main Website URL',
            'value' => (!is_null($website1_record)) ? $website1_record->value : ''
        ]);


        $umbrella_record = $client->uses_umbrella_corp()->first();
        $this->crud->addField([
            'name' => 'details[uses_umbrella_corp]',
            'type' => 'pill-checkbox',
            'left-label' => '',
            'label' => 'Uses Umbrella Corp Warehouse?',
            'tab' => 'Data Warehouse',
            'attributes' => [
                'style' => 'margin-top: 1rem'
            ],
            'default' => (!is_null($umbrella_record)) ? ($umbrella_record->value == 1) : true
        ]);

        // @todo - make a vue-component that toggles on and off the form for custom Data Warehouse Deets.

        $segment_id_record = $client->segment_id()->first();
        $this->crud->addField([
            'name' => 'details[segment_id]',
            'type' => 'text',
            'label' => 'Segment ID',
            'tab' => 'Segment Config',
            'value' => (!is_null($segment_id_record)) ? $segment_id_record->value : ''
        ]);

        $segment_hashtag_record = $client->sentry_project_hashtag()->first();
        $this->crud->addField([
            'name' => 'details[sentry_project_hashtag]',
            'type' => 'text',
            'label' => 'Sentry Project HashTag',
            'placeholder' => '#capeandbay',
            'tab' => 'Sentry Config',
            'value' => (!is_null($segment_hashtag_record)) ? $segment_hashtag_record->value : ''
        ]);

        $ac_record = $client->uses_allcommerce()->first();
        $this->crud->addField([
            'name' => 'details[uses_allcommerce]',
            'type' => 'pill-checkbox',
            'left-label' => '',
            'label' => 'Enable AllCommerce?',
            'tab' => 'Cape & Bay Products',
            'attributes' => [
                'style' => 'margin-top: 1rem'
            ],
            'hint' => 'If enabled, CityCRM will no longer be available.',
            'default' => (!is_null($ac_record)) ? ($ac_record->value == 1) : false
        ]);

        $lb_record = $client->uses_leadbinder()->first();
        $this->crud->addField([
            'name' => 'details[uses_leadbinder]',
            'type' => 'pill-checkbox',
            'left-label' => '',
            'label' => 'Enable LeadBinder?',
            'tab' => 'Cape & Bay Products',
            'attributes' => [
                'style' => 'margin-top: 1rem'
            ],
            'hint' => 'If enabled, CityCRM will no longer be available.',
            'default' => (!is_null($lb_record)) ? ($lb_record->value == 1) : false
        ]);

        $gr_record = $client->uses_gymrevenue()->first();
        $this->crud->addField([
            'name' => 'details[uses_gymrevenue]',
            'type' => 'pill-checkbox',
            'left-label' => '',
            'label' => 'Enable GymRevenue?',
            'tab' => 'Cape & Bay Products',
            'attributes' => [
                'style' => 'margin-top: 1rem'
            ],
            'hint' => 'If enabled, CityCRM will no longer be available.',
            'default' => (!is_null($gr_record)) ? ($gr_record->value == 1) : false
        ]);

        $cc_record = $client->uses_citycrm()->first();
        $this->crud->addField([
            'name' => 'details[uses_citycrm]',
            'type' => 'pill-checkbox',
            'left-label' => '',
            'label' => 'Enable CityCRM?',
            'tab' => 'Cape & Bay Products',
            'attributes' => [
                'style' => 'margin-top: 1rem'
            ],
            'hint' => 'If enabled, all other products will no longer be available.',
            'default' => (!is_null($cc_record)) ? ($cc_record->value == 1) : false
        ]);
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        $data = $request->all();
        if(array_key_exists('details', $data))
        {
            foreach($data['details'] as $col => $val)
            {
                switch($col)
                {
                    default:
                        $record = $this->crud->entry->$col()->first();

                        if(is_null($record))
                        {
                            $record = new ClientDetail();
                            $record->client_id = $this->crud->entry->id;
                            $record->detail = $col;
                        }

                        $record->value = $val;
                        $record->save();
                }
            }
        }

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /*
    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);

        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        // show a success message

        return $redirect_location;//redirect('/crud-clients');
    }


    */
}
