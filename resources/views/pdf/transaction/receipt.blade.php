@extends('pdf.template')
@section('content')
    <div class="body">
        <div>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc blandit accumsan rhoncus. Nulla iaculis interdum
            mauris. Proin urna ex, auctor non orci ac, faucibus aliquam felis. Vestibulum mauris erat, faucibus non tortor
            sit amet, porttitor finibus orci. In ut eros et lorem auctor sodales non eget diam. Quisque in pulvinar nunc,
            nec sagittis sem. Quisque dictum massa et turpis pharetra gravida. Sed sodales diam nec ligula commodo rhoncus.
            Nam quis bibendum leo, ac ornare odio. Ut vitae posuere elit. Vestibulum laoreet velit in nibh ultrices, quis
            rutrum erat rhoncus.
            Duis sem elit, consequat vitae odio id, semper dapibus nibh. Donec eget velit id tellus porttitor ultricies
            aliquam a magna. Vivamus dapibus tortor at varius suscipit. Praesent lobortis lorem suscipit facilisis semper.
            Curabitur in purus odio. Sed urna nibh, imperdiet in augue vel, pulvinar lobortis augue. Fusce dignissim turpis
            augue, et eleifend est vulputate sed. Morbi efficitur justo sit amet sem aliquam, id dapibus velit laoreet.
            Quisque ipsum ex, tincidunt quis purus vestibulum, sodales fermentum nulla. Nam massa nisi, convallis venenatis
            pellentesque quis, euismod ac ligula. Donec sagittis pretium convallis. Class aptent taciti sociosqu ad litora
            torquent per conubia nostra, per inceptos himenaeos.
        </div>
        <div>
            Integer accumsan placerat neque, ut viverra libero sodales sed. Cras orci massa, dictum vitae elit vel, aliquet
            facilisis magna. Fusce suscipit malesuada ante, at viverra mi malesuada et. Duis mollis neque vitae lacus
            cursus, eget lacinia neque rutrum. Suspendisse aliquam posuere turpis, sit amet efficitur nisi sagittis at.
            Praesent congue lectus sed magna semper, a porta augue molestie. In ultrices commodo venenatis. Nulla
            ullamcorper in neque non rutrum. Donec lobortis accumsan nibh et malesuada.
            Fusce in euismod nulla. In id molestie enim. In tortor purus, pulvinar sit amet ultrices ac, interdum nec eros.
            Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam id
            condimentum mauris, non posuere mauris. Aenean tincidunt diam a tortor dignissim tincidunt. Suspendisse a odio
            diam. Curabitur congue consequat iaculis. In efficitur imperdiet orci, eu commodo dui porttitor eget. Nulla in
            magna non nibh interdum varius sit amet eu urna. Nam ullamcorper nibh risus, ut pellentesque ex ornare sit amet.
            Ut quis justo maximus nisi gravida blandit ac vel est. Praesent tristique vestibulum neque, id suscipit nunc
            elementum ac.
        </div>
    </div>

    <style>
        @page {
            size: 80mm 380mm;
            margin: 0;
        }

        *, *::after, *::before {
            box-sizing: border-box;
        }

        /*img {*/
        /*    max-width: 100%;*/
        /*}*/

        .body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            padding: 5mm;
            width: 80mm;
            height: 100%;
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            overflow: hidden;
            word-break: break-word;
            page-break-inside: avoid;
        }

        /*.body {*/
        /*    !*page-break-inside: avoid !important;*!*/
        /*    page-break-inside: avoid;*/
        /*    !*page-break-after: avoid !important;*!*/
        /*    !*break-inside: avoid !important;*!*/
        /*}*/
    </style
@endsection
