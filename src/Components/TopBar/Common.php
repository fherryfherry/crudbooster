<?php

if(!function_exists('cbTopBarComponents')) {
    function cbTopBarComponents() {
        return \CrudBooster\Components\TopBar\TopBarRegistrar::__getData();
    }
}
