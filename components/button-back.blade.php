@if(url()->previous() != url()->current())
  <flux:button variant="ghost" href="{{ url()->previous() }}" wire:navigate>
    Go Back
  </flux:button>
@endif
