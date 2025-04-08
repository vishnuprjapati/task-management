<?php

// services/TemplateEngine.php
class TemplateEngine {
    public static function render($template, $data = []) {
        extract($data);
        ob_start();
        include __DIR__.'/../views/emails/'.$template;
        return ob_get_clean();
    }
}


?>