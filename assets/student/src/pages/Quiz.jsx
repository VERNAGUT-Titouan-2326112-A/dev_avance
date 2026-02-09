import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getQuiz, submitQuiz } from "../api/quizzes";
import QuizCard from "../components/QuizCard";

export default function Quiz() {
    const { courseId } = useParams();
    const [questions, setQuestions] = useState([]);
    const [answers, setAnswers] = useState({});

    useEffect(() => {
        getQuiz(courseId).then(setQuestions);
    }, [courseId]);

    const handleSubmit = async () => {
        await submitQuiz(courseId, answers);
        alert("Quiz envoyé !");
    };

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-6">QCM</h1>

            {questions.map((q) => (
                <QuizCard
                    key={q.id}
                    question={q}
                    onAnswer={(value) =>
                        setAnswers({ ...answers, [q.id]: value })
                    }
                />
            ))}

            <button
                onClick={handleSubmit}
                className="mt-6 bg-green-500 text-white px-6 py-2 rounded"
            >
                Valider
            </button>
        </div>
    );
}
