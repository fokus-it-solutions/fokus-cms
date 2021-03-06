<?php
class Block_4 extends BlockBasic
{
    private $text = '';

    function __construct($static = array(), $dynamic = array())
    {
        parent::__construct($static, $dynamic);
    }
    
    
    public function get()
    {
        $this->text = $this->html;

        $this->html = $this->tidyText($this->html);
        $this->html = $this->buildInternLinks($this->html);  
        
        $this->html = $this->initAttributes($this->html);    
        $this->html = $this->executeCallback();
            
        $output = $this->html_before.$this->html.$this->html_after;
        
        return $output;  
    }

    public function getHookAttributes()
    {
        $self = array(
            'text' => $this->text,
            'html' => $this->html
        );

        return array_merge($self, $this->getHookStandardAttributes());
    }
}   
?>