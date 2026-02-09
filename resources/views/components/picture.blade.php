@props([
    'src' => null,
    'alt' => '',
    'imageClass' => '',
    'icon' => null,
    'iconClass' => null
])

@php $primarySource = $src(); @endphp

@isset($primarySource)
  <picture {{ $attributes->class(['block size-full']) }}>
    <source srcset="{{ $src('avif') }}" type="image/avif">
    <source srcset="{{ $src('webp') }}" type="image/webp">
    <img src="{{ $src() }}"
         alt="{{ $alt }}"
      {{ $attributes->class(["size-full object-cover"]) }}
    />
  </picture>
@elseif(isset($icon))
  <div class="flex size-full items-center justify-center">
    <flux:icon
      :name="$icon"
      variant="solid"
      {{  $attributes->class(['size-10 text-zinc-500 dark:text-zinc-400']) }}
    />
  </div>
@endisset
