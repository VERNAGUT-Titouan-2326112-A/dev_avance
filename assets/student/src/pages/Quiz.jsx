import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom"; // useNavigate pour rediriger
import { getQuiz, submitQuiz } from "../api/quizzes.js";
import QuizCard from "../components/QuizCard";

export default function Quiz() {
    const { id } = useParams();
    const navigate = useNavigate();

    const [quiz, setQuiz] = useState(null);
    const [answers, setAnswers] = useState({});

    const studentId = 1;

    useEffect(() => {
        getQuiz(id).then(setQuiz);
    }, [id]);

    const handleSubmit = async () => {
        let calculatedScore = 0;
        const totalQuestions = quiz.questions.length;

        quiz.questions.forEach((question) => {
            const userAnswerId = answers[question.id];
            const correctAnswer = question.answers.find((a) => a.isCorrect);
            if (correctAnswer && userAnswerId === correctAnswer.id) {
                calculatedScore++;
            }
        });

        // Conversion en note sur 20
        const finalScore = Math.round((calculatedScore / totalQuestions) * 20);

        try {
            // 2. Envoi à l'API
            await submitQuiz(id, finalScore, answers, studentId);
            alert(`Score: ${finalScore}/20. Sauvegardé !`);
            navigate('/results');
        } catch (error) {
            console.error("Erreur lors de l'envoi", error);
            alert("Erreur lors de la sauvegarde.");
        }
    };

    if (!quiz) return <div className="p-6">Chargement...</div>;

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-2">{quiz.nom}</h1>

            {quiz.questions && quiz.questions.map((q) => (
                <QuizCard
                    key={q.id}
                    question={q}
                    onAnswer={(val) => setAnswers({ ...answers, [q.id]: val })}
                />
            ))}

            <button
                onClick={handleSubmit}
                className="mt-6 bg-blue-600 text-white px-6 py-2 rounded"
            >
                Terminer le Quiz
            </button>
        </div>
    );
}