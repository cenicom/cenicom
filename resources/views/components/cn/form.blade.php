<form
    @if($id)
        id="{{ $id }}"
    @endif

    method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"

    action="{{ $action }}"

    class="{{ $class }}">


    @csrf


    @if(!in_array(strtoupper($method), ['GET','POST']))

        @method($method)

    @endif


    {{ $slot }}


</form>
