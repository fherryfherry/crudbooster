@if(isset($confirmTitle))
    {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
@endif
<x-header pageTitle="Query Builder"/>
