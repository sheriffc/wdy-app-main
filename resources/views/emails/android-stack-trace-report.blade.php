<x-mail::message>
# Android Stack Trace Report from {{ env("APP_URL","") }}

## From {{$fromDateTime}} to {{$toDateTime}} {{count($data)}} stack trace reports were received

## Android Build: {{env("ANDROID_BUILD_TYPE", "")}}

below stack traces ordered most recent to least

@foreach($data as $idx=>$item)
---

no {{$idx+1}} | received: {{$item->created_at}}

username: {{$item->username}}

version: {{$item->version_code}}

<x-mail::panel>
@if($item->stack_trace_decoded)
`{{$item->stack_trace_decoded}}`
@else
`{{$item->stack_trace}}`
@endif
</x-mail::panel>
@endforeach
</x-mail::message>
