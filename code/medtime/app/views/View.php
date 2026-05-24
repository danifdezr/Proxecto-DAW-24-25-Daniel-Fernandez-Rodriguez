<?php

namespace app\views;

class View{
    public function showView(string $viewname, ?array $data = null){
        include_once VIEW_PATH."$viewname-view.php";
    }
}