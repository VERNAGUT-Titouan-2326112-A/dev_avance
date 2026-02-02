import api from "../api/axios";

export const getQuiz = async (courseId) => {
    const res = await api.get(`/courses/${courseId}/quiz`);
    return res.data;
};

export const submitQuiz = async (courseId, answers) => {
    return api.post(`/courses/${courseId}/quiz`, { answers });
};
