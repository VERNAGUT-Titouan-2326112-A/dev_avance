export default function DocumentViewer({ url }) {
    return (
        <iframe
            src={url}
            className="w-full h-[500px] border rounded"
        />
    );
}
