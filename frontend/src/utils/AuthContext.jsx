
import React, {
  createContext,
  useContext,
  useEffect,
  useState,
} from "react";

import apiClient from "./apiClient";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Periksa token ketika aplikasi pertama kali dibuka
  useEffect(() => {
    const checkUser = async () => {
      const token = localStorage.getItem("token");

      if (!token) {
        setLoading(false);
        return;
      }

      try {
        const response = await apiClient.get("/profile");
        setUser(response.data);
      } catch (error) {
        localStorage.removeItem("token");
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    checkUser();
  }, []);

  // Login
  const login = async (email, password) => {
    const response = await apiClient.post("/login", {
      email,
      password,
    });

    const { token, user } = response.data;

    localStorage.setItem("token", token);
    setUser(user);

    return user;
  };

  // Register lalu login otomatis
  const register = async (data) => {
    await apiClient.post("/register", data);

    // API register belum mengembalikan token,
    // sehingga login dilakukan setelah pendaftaran berhasil.
    return await login(data.email, data.password);
  };

  // Logout
  const logout = async () => {
    try {
      await apiClient.post("/logout");
    } finally {
      localStorage.removeItem("token");
      setUser(null);
    }
  };

  const value = {
    user,
    loading,
    isAuthenticated: Boolean(user),
    isAdmin: user?.role === "admin",
    login,
    register,
    logout,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
