import api from "./axios.js";

export const getQuiz = async (quizId) => {
    const res = await api.get(`/quizzes/${quizId}`);
    return res.data;
};


export const submitQuiz = async (quizId, score, answers, studentId) => {
    return api.post('/quiz_attempts', {
        qcm: `/api/quizzes/${quizId}`,
        student: `/api/students/${studentId}`,
        score: score,
        answers: JSON.stringify(answers)
    });
};