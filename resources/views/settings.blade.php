@extends('statamic::layout')

@section('title', 'Queryable suggestions')

@section('content')
    <ui-card>
        <settings-form
            :collections="{{ json_encode($collections->pluck('title', 'handle')) }}"
            :sites="{{ json_encode($sites) }}"
            :collection-fields="{{ json_encode($collectionFields) }}"
            :initial-settings="{{ json_encode($initialSettings) }}"
        ></settings-form>
    </ui-card>
@endsection
