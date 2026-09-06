@props(['data'])
{{--
    JSON_HEX_TAG is what keeps a value containing "</script>" from closing this
    block and turning structured data into an injection point. The rest harden
    the same escape for quote- and entity-based variants. All callers pass
    static data today; the component takes whatever it is given tomorrow.
--}}
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
