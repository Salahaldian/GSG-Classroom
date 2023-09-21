<x-alert name="error" type="danger" />
<div class="row">
    <div class="col-md-8">
        <x-form.floating-control name="title">
            <x-slot:label>
                <label for="title">Title</label>
            </x-slot:label>
            <x-form.input name="title" :value="$classwork->title" placeholder="Title" />
        </x-form.floating-control>
        <x-form.floating-control name="description">
            <x-slot:label>
                <label for="description">Description</label>
            </x-slot:label>
            <x-form.textarea name="description" :value="$classwork->description" placeholder="Description" />
        </x-form.floating-control>
    </div>
    <div class="col-md-4">
        <x-form.floating-control name="published_at">
            <x-slot:label>
                <label for="published_at">Published Date</label>
            </x-slot:label>
            <x-form.input name="published_at" :value="$classwork->published_date" type="date" />
        </x-form.floating-control>
        <div class="mb-3">
            @foreach ($classroom->students as $student)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="students[]" value="{{ $student->id }}"
                        id="std-{{ $student->id }}" @checked(!isset($assigned) || in_array($student->id, $assigned))>
                    <label class="form-check-label" for="std-{{ $student->id }}">
                        {{ $student->name }}
                    </label>
                </div>
            @endforeach
        </div>
        @if ($type == 'assignment')
            <x-form.floating-control name="options.grade">
                <x-slot:label>
                    <label for="grade">Grade</label>
                </x-slot:label>
                <x-form.input name="options[grade]" :value="$classwork->options['grade'] ?? ''" type="nubmer" min=0 />
            </x-form.floating-control>
            <x-form.floating-control name="options.due">
                <x-slot:label>
                    <label for="due">Due</label>
                </x-slot:label>
                <x-form.input name="options[due]" :value="$classwork->options['due'] ?? ''" type="date" />
            </x-form.floating-control>
        @endif
        <x-form.floating-control name="topic_id">
            <x-slot:label>
                <label for="topic_id">Topic Id</label>
            </x-slot:label>
            <select class="form-select" name="topic_id" id="topic_id">
                <option value="">No Topic</option>
                    @foreach ($classroom->topics as $topic)
                <option @selected($topic->id == $classwork->topic_id) value="{{ $topic->id }}">{{ $topic->name }}</option>
                    @endforeach
            </select>
        </x-form.floating-control>
    </div>
</div>
@push('scripts')
    <script src="https://cdn.tiny.cloud/1/t1sd8z1xsofdfxiv61aj611o3o6jgu1m4zln006goc1qn9y2/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                "See docs to implement AI Assistant"))
        });
    </script>
@endpush
