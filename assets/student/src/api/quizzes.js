import api from "./axios.js";

export const getQuiz = async (quizId) => {
    const res = await api.get(`/quiz/${quizId}`);
    return res.data;
};

export const getStudentResults = async (userId) => {
    const res = await api.get(`/quiz_attempts?student=${userId}`);
    return res.data['hydra:member'] || res.data['member'] || res.data;
};

export const submitQuiz = async (quizId, score, answers, userId) => {
    return api.post('/quiz_attempts', {
        qcm: `/api/quiz/${quizId}`,
        student: `/api/users/${userId}`,
        score: score,
        answers: JSON.stringify(answers),
        submittedAt: new Date().toISOString()
    });
};