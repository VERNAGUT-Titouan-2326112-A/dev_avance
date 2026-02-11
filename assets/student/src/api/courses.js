import api from "./axios.js";

export const getCourses = async () => {
    const res = await api.get("/courses");
    return res.data['hydra:member'];
};

export const getCourseById = async (id) => {
    const res = await api.get(`/courses/${id}`);
    return res.data;
};