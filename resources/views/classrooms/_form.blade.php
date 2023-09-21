<x-alert name="error" id="error" class="alert-danger" />
<x-form.floating-control  name="name">
    <x-slot:label>
        <label for="name">Classroom Name</label>
    </x-slot:label>
    <x-form.input name="name" class="form-control-lg" :value="$classroom->name" placeholder="Classroom Name" />
</x-form.floating-control>

<x-form.floating-control name="section">
    <x-slot:label>
        <label for="section">Section</label>
    </x-slot:label>
    <x-form.input name="section" :value="$classroom->section" placeholder="Section" />
</x-form.floating-control>

<x-form.floating-control name="subject">
    <x-slot:label>
        <label for="subject">Subject</label>
    </x-slot:label>
    <x-form.input name="subject" :value="$classroom->subject" placeholder="Subject" />
</x-form.floating-control>

<x-form.floating-control name="room">
    <x-slot:label>
        <label for="room">Room</label>
    </x-slot:label>
    <x-form.input name="room" :value="$classroom->room" placeholder="room" />
</x-form.floating-control>

<x-form.floating-control name="cover_image">
    <x-slot:label>
        <label for="name">Classroom Name</label>
    </x-slot:label>
    @if ($classroom->cover_image_path)
        <img src="{{ Storage::disk('public')->url($classroom->cover_image_path) }}" alt="">
    @endif
    <x-form.input type="file" name="cover_image" :value="$classroom->cover_image_path" placeholder="Cover Image" />
    @error('cover_image')
        <div class="invalid-feedback">{{ $message }}.</div>
    @enderror
</x-form.floating-control>
<button type="submit" class="btn btn-primary">{{ $button_label }}</button>
