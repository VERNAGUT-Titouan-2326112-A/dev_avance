import { useEffect, useState } from "react";
import { getCourses } from "../api/courses";
import CourseCard from "../components/CourseCard";

export default function Courses() {
    const [courses, setCourses] = useState([]);

    useEffect(() => {
        getCourses().then(setCourses);
    }, []);

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-6">Mes cours</h1>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {courses.map((course) => (
                    <CourseCard key={course.id} course={course} />
                ))}
            </div>
        </div>
    );
}
