<?php
declare(strict_types=1);

class ContactForm {
    protected $errors = [];
    protected $data = [];
    protected $inputs = [];
    protected $isValid;
    protected $fields = [
        'email_address' => [
            'alias' => 'Email Address',
            'filter' => FILTER_VALIDATE_EMAIL,
        ],
        'FirstName' => [
            'alias' => 'First Name',
            'filter' => FILTER_SANITIZE_STRING,
        ],
        'LastName' => [
            'alias' => 'Last Name',
            'filter' => FILTER_SANITIZE_STRING,
        ],
    ];

    public function __construct($data = [])
    {
        $this->data = $data;
        $this->isValid = false;
    }
    public function validate() : void 
    {
        $filters = $this->getFilters();
        $this->inputs = filter_var_array($this->data, $filters);
        $this->setErrors();
        $this->isValid = empty($this->errors);
    }
    
    public function getErrors() : array
    {
        return $this->errors;
    }
    
    public function getInputs() : array 
    {   
        return $this->inputs;
    }

    public function isValid() : bool {
        return $this->isValid;
    }

    protected function setErrors() : void 
    {
        $this->errors = [];
        foreach ($this->inputs as $key => $value) {
            if (empty($value)) {
                $this->errors[$key] = "Please enter a valid {$this->fields[$key]['alias']}";
            }
        }
    }

    protected function getFilters() : array
    {
        $filters = [];
        foreach ($this->fields as $key => $field) {
            $filters[$key] = $field['filter'];
        }
        return $filters;
    }
}