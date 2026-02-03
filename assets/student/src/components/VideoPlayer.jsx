export default function VideoPlayer({ url }) {
    return (
        <div className="aspect-video w-full">
            <iframe
                src={url}
                className="w-full h-full rounded"
                allowFullScreen
            />
        </div>
    );
}
