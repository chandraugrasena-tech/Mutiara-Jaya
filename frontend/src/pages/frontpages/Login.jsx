
import { useState } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "../../utils/AuthContext";

export default function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [remember, setRemember] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      const user = await login(email, password);

      if (user.role === "admin") {
        navigate("/admin/dashboard", { replace: true });
      } else {
        navigate(location.state?.from || "/", { replace: true });
      }
    } catch (err) {
      setError(
        err.response?.data?.message ||
        err.response?.data?.errors?.email?.[0] ||
        "Email atau password salah."
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-5">
      <div className="text-center">
        <h1 className="text-2xl font-extrabold text-[#165823]">
          Masuk ke Mutiara Jaya
        </h1>
        <p className="text-xs text-gray-500 mt-1">
          Masuk untuk memesan dan memantau pesananmu.
        </p>
      </div>

      {location.state?.notice && (
        <div className="bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs font-semibold rounded-xl p-3">
          {location.state.notice}
        </div>
      )}

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl p-3">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-bold text-gray-700 mb-1">
            Email
          </label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            autoFocus
            className="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none"
          />
        </div>

        <div>
          <label className="block text-sm font-bold text-gray-700 mb-1">
            Password
          </label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none"
          />
        </div>

        <label className="flex items-center gap-2 text-xs text-gray-600">
          <input
            type="checkbox"
            checked={remember}
            onChange={(e) => setRemember(e.target.checked)}
            className="rounded border-gray-300"
          />
          Ingat saya
        </label>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3 rounded-xl cursor-pointer disabled:opacity-60"
        >
          {loading ? "Memproses..." : "Masuk"}
        </button>
      </form>

      <p className="text-center text-xs text-gray-500">
        Belum punya akun?{" "}
        <Link to="/register" className="font-bold text-[#165823] underline">
          Daftar
        </Link>
      </p>
    </div>
  );
}
