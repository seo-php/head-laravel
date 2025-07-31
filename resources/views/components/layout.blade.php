<!DOCTYPE html>
<html {!! $htmlAttrs !!}>
    <head>
        {!! $headTags !!}
    </head>
    <body {!! $bodyAttrs !!}>
        {!! $bodyOpenTags !!}

        {{ $slot }}

        {!! $bodyCloseTags !!}
    </body>
</html>
