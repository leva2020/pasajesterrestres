<?php
namespace Register\Form;

use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element;
use Zend\Form\Element\Checkbox;
use Tools\Model\DocumentTypeTable;

class UserForm extends Form
{

    protected $documentTypeTable;

    protected $selectId = array();

    private $documentTypes;

    private $documentTypesType;

    public function __construct()
    {
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-content');
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
        
        $this->add(array(
            'name' => 'uid',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
                
        $this->add(array(
            'name' => 'first_name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'first_name',
                'maxlength' => 200,
                'class' => 'inp-txt-1',
                'placeholder' => 'Nombres'
            ),
            'options' => array(
                'label' => 'Nombres',
                'label_attributes' => array(
                    'class' => 'txt-1',
                    'type' => 'p'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'last_name',
                'maxlength' => 200,
                'class' => 'inp-txt-1',
                'placeholder' => 'Apellidos'
            ),
            'options' => array(
                'label' => 'Apellidos',
                'label_attributes' => array(
                    'class' => 'txt-1'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'id' => 'email',
                'maxlength' => 100,
                'class' => 'inp-txt-1',
                'placeholder' => 'Correo electrónico'
            ),
            'options' => array(
                'label' => 'Correo electrónico',
                'label_attributes' => array(
                    'class' => 'txt-1'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password',
                'maxlength' => 64,
                'class' => 'inp-txt-1',
                'placeholder' => 'Contraseña'
            ),
            'options' => array(
                'label' => 'Contraseña',
                'label_attributes' => array(
                    'class' => 'txt-1'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'password_repeat',
            'attributes' => array(
                'type' => 'password',
                'class' => 'inp-txt-1',
                'placeholder' => 'Confirma tu contraseña'
            ),
            'options' => array(
                'label' => 'Confirma tu contraseña',
                'label_attributes' => array(
                    'class' => 'txt-1'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'terms',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => ''
            ),
            'attributes' => array(
                'id' => 'terms',
                'class' => 'checkbox-1',
                'value' => ''
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'CONTINUAR',
                'id' => 'submitbutton',
                'class' => 'btn-4 btn-continue-1'
            )
        ));
    }
}