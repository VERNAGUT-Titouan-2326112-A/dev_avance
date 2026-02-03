import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getCourseById } from "../api/courses";
import VideoPlayer from "../components/VideoPlayer";
import DocumentViewer from "../components/DocumentViewer";

export default function CourseDetail() {
    const { id } = useParams();
    const [course, setCourse] = useState(null);

    useEffect(() => {
        getCourseById(id).then(setCourse);
    }, [id]);

    if (!course) return <p>Chargement...</p>;

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-4">{course.title}</h1>

            <div className="mb-6">
                <VideoPlayer url={course.videoUrl} />
            </div>

            <div>
                <h2 className="text-xl font-semibold mb-2">Documents</h2>
                <DocumentViewer url={course.documentUrl} />
            </div>
        </div>
    );
}
