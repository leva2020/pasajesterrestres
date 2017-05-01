<?php
namespace Register\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface
{

    private $id;

    private $username;

    private $firstName;

    private $lastName;

    private $email;

    private $password;

    private $salt;

    private $userTypeId;

    private $document;

    private $documentTypeId;

    private $uid;

    private $status;

    private $registerDate;

    private $updateDate;

    protected $inputFilter;
    
    // public function __construct(TableGateway $tableGateway)
    // {
    // $this->tableGateway = $tableGateway;
    // }
    public function exchangeArray($data)
    {
        if (array_key_exists('id', $data))
            $this->setId($data['id']);
        if (array_key_exists('first_name', $data))
            $this->setFirstName($data['first_name']);
        if (array_key_exists('last_name', $data))
            $this->setLastName($data['last_name']);
        if (array_key_exists('username', $data))
            $this->setUsername($data['username']);
        if (array_key_exists('password', $data))
            $this->setPassword($data['password']);
        if (array_key_exists('email', $data))
            $this->setEmail($data['email']);
        if (array_key_exists('document', $data))
            $this->setDocument($data['document']);
        if (array_key_exists('register_date', $data))
            $this->setRegisterDate($data['register_date']);
        if (array_key_exists('update_date', $data))
            $this->setUpdateDate($data['update_date']);
        if (array_key_exists('uid', $data))
            $this->setUid($data['uid']);
        if (array_key_exists('status', $data))
            $this->setStatus($data['status']);
        if (array_key_exists('salt', $data))
            $this->setSalt($data['salt']);
        if (array_key_exists('document_type_id', $data))
            $this->setDocumentTypeId($data['document_type_id']);
        if (array_key_exists('user_types_id', $data))
            $this->setUserTypeId($data['user_types_id']);
        if (array_key_exists('document_type_name', $data))
            $this->setDocumentTypeName($data['document_type_name']);
        if (array_key_exists('user_type_name', $data))
            $this->setUserTypeName($data['user_type_name']);
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'first_name',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'El campo es obligatorio'
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 200,
                            'messages' => array(
                                \Zend\Validator\StringLength::TOO_SHORT => 'Debes ingresar al menos 2 caracteres.',
                                \Zend\Validator\StringLength::TOO_LONG => 'Debes ingresar máximo 200 caracteres.'
                            )
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'last_name',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'El campo es obligatorio'
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 200,
                            'messages' => array(
                                \Zend\Validator\StringLength::TOO_SHORT => 'Debes ingresar al menos 2 caracteres.',
                                \Zend\Validator\StringLength::TOO_LONG => 'Debes ingresar máximo 200 caracteres.'
                            )
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 100,
                            'messages' => array(
                                \Zend\Validator\StringLength::TOO_SHORT => 'Esta no es una contraseña válida'
                            )
                        )
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => ''
                            )
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'password_repeat',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => ''
                            )
                        )
                    )
                )
            )));
            
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'email',
                        'required' => true,
                        'filters' => array(
                            array(
                                'name' => 'StripTags'
                            ),
                            array(
                                'name' => 'StringTrim'
                            )
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress'
                            ),
                            array(
                                'name' => 'NotEmpty'
                            ),
                            array(
                                'name' => 'Db\NoRecordExists',
                                'options' => array(
                                    'table' => 'users',
                                    'field' => 'email',
                                    'adapter' => \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter(),
                                    'messages' => array(
                                        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => "El correo electrónico que ingresaste ya está asociado a otra cuenta de un usuario, ingresa uno nuevo."
                                    )
                                )
                            
                            )
                        )
                    )
                )
            );
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'terms',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Digits',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Digits::NOT_DIGITS => 'Debes aceptar los términos y condiciones para poder continuar.'
                            )
                        )
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Debes aceptar los términos y condiciones para poder continuar.'
                            )
                        )
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param field_type $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return the $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     * @param field_type $username            
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     *
     * @return the $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param field_type $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return the $lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     *
     * @param field_type $lastName            
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param field_type $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @return the $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @param field_type $password            
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     *
     * @return the $salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     *
     * @param field_type $salt            
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     *
     * @return the $userTypeId
     */
    public function getUserTypeId()
    {
        return $this->userTypeId;
    }

    /**
     *
     * @param field_type $userTypeId            
     */
    public function setUserTypeId($userTypeId)
    {
        $this->userTypeId = $userTypeId;
    }

    /**
     *
     * @return the $document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     *
     * @param field_type $document            
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     *
     * @return the $documentTypeId
     */
    public function getDocumentTypeId()
    {
        return $this->documentTypeId;
    }

    /**
     *
     * @param field_type $documentTypeId            
     */
    public function setDocumentTypeId($documentTypeId)
    {
        $this->documentTypeId = $documentTypeId;
    }

    /**
     *
     * @return the $uid
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     *
     * @param field_type $uid            
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     *
     * @return the $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param field_type $status            
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return the $registerDate
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     *
     * @param field_type $registerDate            
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     *
     * @return the $updateDate
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     *
     * @param field_type $updateDate            
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    public function encryptSecret()
    {
        $this->salt = OAuthTools::genSalt();
        $this->password = OAuthTools::oAuthHash($this->getPassword(), $this->getSalt());
    }

    public function generateUid($document, $generate = true)
    {
        $uid = $document;
        if ($generate) {
            $uid = (! empty($document)) ? sha1($document . uniqid($document)) : false;
        }
        return $uid;
    }
}
