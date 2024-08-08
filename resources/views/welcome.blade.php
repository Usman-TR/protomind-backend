<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
    </head>
    <body class="antialiased">
    <video id="videoPlayer" controls></video>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const mediaId = 148;
        const mimeCodec = 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"';

        if ('MediaSource' in window && MediaSource.isTypeSupported(mimeCodec)) {
            const mediaSource = new MediaSource();
            videoPlayer.src = URL.createObjectURL(mediaSource);

            mediaSource.addEventListener('sourceopen', sourceOpen);
        } else {
            console.error('Unsupported MIME type or codec: ', mimeCodec);
        }

        function sourceOpen() {
            const mediaSource = this;
            const sourceBuffer = mediaSource.addSourceBuffer(mimeCodec);
            fetchVideo(sourceBuffer);
        }

        async function fetchVideo(sourceBuffer) {
            try {
                const response = await fetch(`api/stream/${mediaId}`);
                const reader = response.body.getReader();

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    sourceBuffer.appendBuffer(value);
                    await new Promise(resolve => {
                        if (sourceBuffer.updating) {
                            sourceBuffer.addEventListener('updateend', resolve, { once: true });
                        } else {
                            resolve();
                        }
                    });
                }

                mediaSource.endOfStream();
            } catch (error) {
                console.error('Error fetching video:', error);
            }
        }
    </script>

    </body>
</html>
