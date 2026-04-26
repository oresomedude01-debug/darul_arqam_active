@php
    use App\Helpers\LocalizationHelper;
    $currentLocale = LocalizationHelper::getCurrentLocale();
    $availableLocales = LocalizationHelper::getAvailableLocales();
@endphp

<div class="locale-switcher">
    @foreach($availableLocales as $locale)
        @if($locale !== $currentLocale)
            <a href="{{ route('locale.switch', ['locale' => $locale]) }}" 
               class="locale-switch-link {{ $locale === $currentLocale ? 'active' : '' }}"
               title="{{ LocalizationHelper::getLocaleDisplayName($locale) }}">
                {{ LocalizationHelper::getLocaleDisplayName($locale) }}
            </a>
        @endif
    @endforeach
</div>
