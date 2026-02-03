import { Link } from "react-router-dom";

export default function CourseCard({ course }) {
    return (
        <div className="border rounded p-4 shadow">
            <h2 className="font-semibold text-lg">{course.title}</h2>
            <p className="text-gray-600 mb-3">{course.description}</p>

            <Link
                to={`/courses/${course.id}`}
                className="bg-blue-500 text-white px-4 py-2 rounded"
            >
                Voir le cours
            </Link>
        </div>
    );
}
