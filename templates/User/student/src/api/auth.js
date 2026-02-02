import api from "../api/axios";

export const login = async (email, password) => {
    const res = await api.post("/login", { email, password });
    localStorage.setItem("token", res.data.token);
    return res.data.user;
};

export const logout = () => {
    localStorage.removeItem("token");
};

export const getMe = async () => {
    const res = await api.get("/me");
    return res.data;
};
