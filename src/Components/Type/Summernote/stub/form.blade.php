<div class="summernote-container" 
     x-data="summernote{{$column['key']}}()"
     x-init="init()"
     wire:ignore>
    <textarea id="{{$column['key']}}"
              name="{{$column['key']}}"
              class="form-control"
              wire:model="formData.{{$column['key']}}"
              style="display: none;">{{ $column['value'] ?? '' }}</textarea>
</div>

<script>
function summernote{{$column['key']}}() {
    return {
        editorId: '{{$column['key']}}',
        isInitialized: false,
        
        init() {
            // Wait for DOM to be ready and Livewire to be loaded
            this.$nextTick(() => {
                setTimeout(() => this.initSummernote(), 50);
            });
            
            // Listen for Livewire events
            document.addEventListener('livewire:load', () => {
                setTimeout(() => this.initSummernote(), 50);
            });
        },
        
        initSummernote() {
            const textarea = document.getElementById(this.editorId);
            if (!textarea) return;
            
            const $textarea = $(textarea);
            
            // Destroy existing editor if it exists
            if ($textarea.next('.note-editor').length) {
                $textarea.summernote('destroy');
                this.isInitialized = false;
            }
            
            // Get initial content from textarea value or Livewire data
            let initialContent = textarea.value || '{{ addslashes($column['value'] ?? '') }}';
            
            // If still empty, try to get from Livewire component
            if (!initialContent && window.Livewire) {
                const component = window.Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                if (component && component.get) {
                    initialContent = component.get('formData.{{$column['key']}}') || '';
                }
            }
            
            $textarea.summernote({
                height: {{ $column['option']['height'] ?? 300 }},
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onInit: () => {
                        // Set initial content if exists
                        if (initialContent && initialContent.trim() !== '') {
                            $textarea.summernote('code', initialContent);
                        }
                        this.isInitialized = true;
                    },
                    onChange: (contents, $editable) => {
                        // Update hidden textarea
                        textarea.value = contents;
                        @this.set('formData.{{$column['key']}}', contents, false);
                    },
                    onImageUpload: (files) => {
                        this.uploadSummernoteImage(files);
                    },
                    onPaste: (e) => {
                        @if(($column['option']['auto_reformat'] ?? 'false') === 'true')

                        setTimeout(() => {
                            const content = $textarea.summernote('code');
                            const cleanContent = this.cleanPastedContent(content);
                            $textarea.summernote('code', cleanContent);
                        }, 100);
                        
                        @endif
                    }
                }
            });
        },
        
        uploadSummernoteImage(files) {
            for (let i = 0; i < files.length; i++) {
                this.uploadFile(files[i]);
            }
        },
        
        uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("summernote.upload.image") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(`#${this.editorId}`).summernote('insertImage', data.url);
                } else {
                    console.error('Upload failed:', data.message);
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
            });
        },
        
        cleanPastedContent(content) {
            return content
                .replace(/<o:p[^>]*>/g, '')
                .replace(/<\/o:p>/g, '')
                .replace(/<w:[^>]*>/g, '')
                .replace(/<\/w:[^>]*>/g, '')
                .replace(/<m:[^>]*>/g, '')
                .replace(/<\/m:[^>]*>/g, '')
                .replace(/<v:[^>]*>/g, '')
                .replace(/<\/v:[^>]*>/g, '')
                .replace(/<st1:[^>]*>/g, '')
                .replace(/<\/st1:[^>]*>/g, '')
                .replace(/<xml[^>]*>/g, '')
                .replace(/<\/xml>/g, '')
                .replace(/<html[^>]*>/g, '')
                .replace(/<\/html>/g, '')
                .replace(/<body[^>]*>/g, '')
                .replace(/<\/body>/g, '')
                .replace(/<head[^>]*>[\s\S]*?<\/head>/g, '')
                .replace(/<meta[^>]*>/g, '')
                .replace(/<link[^>]*>/g, '')
                .replace(/<title[^>]*>[\s\S]*?<\/title>/g, '')
                .replace(/<style[^>]*>[\s\S]*?<\/style>/g, '')
                .replace(/<script[^>]*>[\s\S]*?<\/script>/g, '')
                .replace(/<!--[\s\S]*?-->/g, '')
                .replace(/\s*style="[^"]*"/g, '')
                .replace(/\s*class="[^"]*"/g, '')
                .replace(/\s*id="[^"]*"/g, '')
                .replace(/\s*data-[^=]*="[^"]*"/g, '')
                .replace(/\s*on\w+="[^"]*"/g, '')
                .replace(/\n\s*\n/g, '</p><p>')
                .replace(/^([^<])/, '<p>$1')
                .replace(/([^>])$/, '$1</p>')
                .replace(/<p>\s*<\/p>/g, '');
        }
    };
}
</script>