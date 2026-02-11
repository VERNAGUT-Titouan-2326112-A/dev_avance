import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { getQuiz, submitQuiz } from "../api/quizzes.js";
import QuizCard from "../components/QuizCard.jsx";

export default function Quiz({ userId }) {
    const { id } = useParams();
    const navigate = useNavigate();

    const [quiz, setQuiz] = useState(null);
    const [answers, setAnswers] = useState({});

    useEffect(() => {
        getQuiz(id).then(data => {
            console.log("CHARGEMENT QUIZ - Données brutes reçues :", data);
            setQuiz(data);
        });
    }, [id]);

    const handleSubmit = async () => {
        if (!quiz || !quiz.questions) return;

        console.log("--- DÉBUT DU CALCUL ---");
        let calculatedScore = 0;
        const totalQuestions = quiz.questions.length;

        quiz.questions.forEach((question) => {
            // 1. Récupération de la réponse utilisateur (String pour être sûr)
            const userAnswerId = answers[question.id] || answers[String(question.id)];

            console.log(`Question ID: ${question.id}`);
            console.log(`> Réponse choisie par l'utilisateur :`, userAnswerId);

            // 2. Recherche de la bonne réponse dans les données du quiz
            // On accepte 'correct' (JSON API) OU 'isCorrect' (au cas où)
            // On accepte true (bool) OU 1 (int)
            const correctAnswer = question.answers.find((a) => {
                const isIt = a.correct === true || a.correct === 1 || a.isCorrect === true || a.isCorrect === 1;
                if (isIt) console.log(`> Bonne réponse trouvée dans les données : ID ${a.id} (correct=${a.correct}, isCorrect=${a.isCorrect})`);
                return isIt;
            });

            if (!correctAnswer) {
                console.error(`> ERREUR CRITIQUE : Aucune réponse marquée comme "correct" ou "isCorrect" pour la question ${question.id} !`);
            }

            // 3. Comparaison
            if (correctAnswer && userAnswerId != null) {
                // Comparaison souple (==) pour gérer "25" == 25
                if (userAnswerId == correctAnswer.id) {
                    console.log("> RÉSULTAT : ✅ GAGNÉ (+1 point)");
                    calculatedScore++;
                } else {
                    console.log(`> RÉSULTAT : ❌ PERDU (Comparaison: ${userAnswerId} != ${correctAnswer.id})`);
                }
            } else {
                console.log("> RÉSULTAT : ⚠️ PAS DE RÉPONSE ou DONNÉE MANQUANTE");
            }
            console.log("-----------------------");
        });

        const finalScore = totalQuestions > 0
            ? Math.round((calculatedScore / totalQuestions) * 20)
            : 0;

        console.log(`SCORE FINAL CALCULÉ : ${finalScore}/20`);

        try {
            await submitQuiz(id, finalScore, answers, userId);
            alert(`Score: ${finalScore}/20. Sauvegardé avec succès !`);
            navigate('/results');
        } catch (error) {
            console.error("Erreur API:", error.response?.data);
            alert("Erreur lors de la sauvegarde.");
        }
    };

    if (!quiz) return <div className="p-6">Chargement...</div>;

    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold mb-2">{quiz.nom}</h1>
            <p className="text-gray-600 mb-6">{quiz.theme}</p>

            {quiz.questions && quiz.questions.map((q) => (
                <QuizCard
                    key={q.id}
                    question={q}
                    onAnswer={(val) => setAnswers(prev => ({ ...prev, [q.id]: val }))}
                    selectedAnswer={answers[q.id]}
                />
            ))}

            <button
                onClick={handleSubmit}
                className="mt-6 bg-blue-600 text-white px-6 py-2 rounded w-full"
            >
                Terminer le Quiz et enregistrer ma note
            </button>
        </div>
    );
}