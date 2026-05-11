@extends('layouts.app')

@section('custom_css')
{{--    @include('assets.datatables-css')--}}
@endsection

@section('custom_js')
{{--    @include('assets.datatables-js')--}}
@endsection


@section('content')

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center"><h3>Remote Stack Trace Reports -- Latest 100</h3></div>
                        <table id="dt-users" data-csrf="{{csrf_token()}}" class="table table-sm small-font table-responsive-lg" style="width:100%">
                            <thead>
                            <tr>
                                <th class="text-left">Created At</th>
                                <th class="text-left">User ID</th>
                                <th class="text-left">Username</th>
                                <th class="text-left">App Version</th>
                                <th class="text-left">Preview</th>
                                <th class="text-left">View</th>
                                <th class="text-left">Copy</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($records as $el=>$rec)
                                <tr>
                                    <td>{{$rec->created_at}}</td>
                                    <td>{{$rec->user_id}}</td>
                                    <td>{{$rec->username}}</td>
                                    <td>{{$rec->version_code}}</td>
                                    <td>
                                        {{ Str::limit($rec->stack_trace,100)}}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn btn-info" data-bs-toggle="collapse" data-bs-target="#collapse{{$rec->id}}" aria-expanded="false" aria-controls="collapse{{$rec->id}}">View</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn btn-outline-dark copy-button" type="button" data-el="{{$el}}">Copy</button>
                                    </td>
                                </tr>
                                <tr class="collapse" id="collapse{{$rec->id}}">
                                    <td colspan="7" id="stack-trace-{{$el}}"><code>{{$rec->stack_trace}}</code></td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.copy-button').bind('click', function() {

            let el = $(this).data('el')
            let textToCopy = $('#stack-trace-'+el).text();

            //try the first method
            try {
                const storage = document.createElement('textarea');
                storage.value = textToCopy;
                storage.select();
                storage.setSelectionRange(0, 99999);
                var success = document.execCommand('copy');
                document.body.removeChild(storage);
                if (success) {
                    console.log('Text copied to clipboard!');
                } else {
                    copyToClipboard(textToCopy)
                }
            } catch (err) {
                copyToClipboard(textToCopy)
            }

        });

        //second method
        async function copyToClipboard(text){
            try {
                // Write the text to the clipboard
                await navigator.clipboard.writeText(text);
                // Show a success message
                console.log('Text copied to clipboard!');
            } catch (err) {
                // Handle any errors that occur
                console.error('Error copying text to clipboard:', err);
            }
        }

    </script>
@endsection
