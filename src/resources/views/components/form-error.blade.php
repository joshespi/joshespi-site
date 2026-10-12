@props(['field'])
{{-- The id is what @invalid points the field's aria-describedby at. red-700, not 500:
     500 on white is under the 4.5:1 contrast minimum for text this small. --}}
@error($field)
<p id="{{ $field }}-error" class="text-red-700 text-xs mt-1">{{ $message }}</p>
@enderror
