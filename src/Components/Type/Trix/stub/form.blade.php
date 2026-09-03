{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<div >
    <div wire:ignore>
        <trix-editor
            class="formatted-content"
            id="trix-editor-{{$column['key']}}"
            x-data
            x-on:trix-change="$dispatch('formData.{{$column['key']}}', event.target.value)"
            x-ref="trix"
            wire:model.debounce.60s="formData.{{$column['key']}}"
            wire:key="trix-input-{{$column['key']}}"
        ></trix-editor>
    </div>
</div>

<script>
    function initializeTrixEditor{{$column['key']}}() {
        console.log('initializeTrixEditor{{$column['key']}}');
        let targetElement = document.getElementById('trix-editor-{{$column['key']}}');
        document.addEventListener('livewire:load', function () {
            targetElement.disabled = true;
        });

        // Handle file attachment
        targetElement.addEventListener('trix-attachment-add', function(event) {
            if (event.attachment.file) {
                uploadFile(event.attachment);
            }
        });

        targetElement.addEventListener("trix-file-accept", function(event) {
            const allowedTypes = ["image/png", "image/jpeg", "image/gif"];
            if (!allowedTypes.includes(event.file.type)) {
                event.preventDefault();
                alert("Only image files are allowed.");
                event.preventDefault();
            }
        })

        function uploadFile(attachment) {
            let formData = new FormData();
            if (!attachment.file.type.startsWith('image/')) {
                alert('Only image files are allowed.');
                return;
            }
            formData.append('file', attachment.file);

            fetch('{{ route('trix.upload.image') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
              .then(data => {
                  attachment.setAttributes({
                      url: data.url,
                      href: data.url
                  });
              }).catch(error => {
                  console.error('Error uploading file:', error);
              });
        }
    }

    initializeTrixEditor{{$column['key']}}();
</script>
