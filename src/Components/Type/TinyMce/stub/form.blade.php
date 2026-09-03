{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<div>
    <div wire:ignore class="mt-1">
    <textarea
        id="tinymce-{{$column['key']}}"
        x-data="{}"
        x-init="
        (() => {
            if (tinymce.get('tinymce-{{$column['key']}}')) {
                tinymce.get('tinymce-{{$column['key']}}').remove();
            }
            let debounceTimer;
            tinymce.init({
                selector: '#tinymce-{{$column['key']}}',
                language: '{{config('app.locale') ?? 'en'}}',
                readonly: {{$column['readonly'] ? 'true' : 'false'}},
                placeholder: '{{$column['placeholder'] ?? ''}}',
                height: {{$column['option']['height'] ?? 500}},
                license_key: '{{config('cb.tinymce_key') ?? 'gpl'}}',
                plugins: 'image table codesample link lists emoticons',
                toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | image | emoticons',
                setup: function (editor) {
                    editor.on('change input', function () {
                        const content = editor.getContent();
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            $wire.set('formData.{{$column['key']}}', content);
                        }, {{$column['live'] ?? 300}});
                    });
                },
                images_upload_url: '{{ route('tinymce.upload.image') }}'
            });
        })()
    "
        wire:model.live.debounce.500ms="formData.{{$column['key']}}"
        wire:key="tinymce-{{$column['key']}}"
    >{{$value}}</textarea>
    </div>
</div>



