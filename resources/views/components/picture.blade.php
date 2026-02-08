@props([
    'src' => null,
    'alt' => '',
    'pictureClass' => '',
    'imgClass' => '',
])

@php
$resolvedSrc = $src !== null && trim((string) $src) !== '' ? (string) $src : null;
$isGif = false;
$sourceUrls = [];

if ($resolvedSrc !== null) {
    $parsedUrl = parse_url($resolvedSrc);
    $parsedPath = $parsedUrl['path'] ?? '';
    $extension = strtolower(pathinfo($parsedPath, PATHINFO_EXTENSION));
    $isGif = $extension === 'gif';

    if (! $isGif && $extension !== '') {
        $directory = pathinfo($parsedPath, PATHINFO_DIRNAME);
        $filename = pathinfo($parsedPath, PATHINFO_FILENAME);
        $normalizedDirectory = $directory === '.' ? '' : $directory;
        $basePath = $normalizedDirectory === '' ? $filename : "{$normalizedDirectory}/{$filename}";

        $buildVariantUrl = static function (array $urlParts, string $path): string {
            $url = '';

            if (isset($urlParts['scheme'])) {
                $url .= $urlParts['scheme'].'://';
            }

            if (isset($urlParts['host'])) {
                if (isset($urlParts['user'])) {
                    $url .= $urlParts['user'];

                    if (isset($urlParts['pass'])) {
                        $url .= ':'.$urlParts['pass'];
                    }

                    $url .= '@';
                }

                $url .= $urlParts['host'];

                if (isset($urlParts['port'])) {
                    $url .= ':'.$urlParts['port'];
                }
            }

            $url .= $path;

            if (isset($urlParts['query'])) {
                $url .= '?'.$urlParts['query'];
            }

            if (isset($urlParts['fragment'])) {
                $url .= '#'.$urlParts['fragment'];
            }

            return $url;
        };

        $sourceUrls['avif'] = $buildVariantUrl($parsedUrl, "{$basePath}.avif");
        $sourceUrls['webp'] = $buildVariantUrl($parsedUrl, "{$basePath}.webp");
    }
}
@endphp

@if($resolvedSrc !== null)
<picture @if($pictureClass !== '') class="{{ $pictureClass }}" @endif>
  @if(! $isGif)
  @if(isset($sourceUrls['avif']))
  <source srcset="{{ $sourceUrls['avif'] }}" type="image/avif">
  @endif
  @if(isset($sourceUrls['webp']))
  <source srcset="{{ $sourceUrls['webp'] }}" type="image/webp">
  @endif
  @endif

  <img
    src="{{ $resolvedSrc }}"
    alt="{{ $alt }}"
    {{ $attributes->class([$imgClass]) }} />
</picture>
@endif
