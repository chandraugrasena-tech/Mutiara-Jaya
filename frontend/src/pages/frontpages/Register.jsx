import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../utils/AuthContext";

export default function Register() {
  const { register } = useAuth();
  const navigate = useNavigate();

  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
  });

  const [error, setError] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setFieldErrors({});

    if (form.password.length < 6) {
      setFieldErrors({
        password: ["Password minimal 6 karakter."],
      });
      return;
    }

    if (form.password !== form.password_confirmation) {
      setFieldErrors({
        password_confirmation: ["Ulangi password dengan benar."],
      });
      return;
    }

    setLoading(true);

    try {
      const user = await register({
        name: form.name,
        email: form.email,
        phone: form.phone,
        password: form.password,
      });

      if (user.role === "admin") {
        navigate("/admin/dashboard", { replace: true });
      } else {
        navigate("/", { replace: true });
      }
    } catch (err) {
      const errors = err.response?.data?.errors;

      if (errors) {
        setFieldErrors(errors);
      } else {
        setError(
          err.response?.data?.message ||
            "Pendaftaran gagal. Silakan coba lagi."
        );
      }
    } finally {
      setLoading(false);
    }
  };

  const fields = [
    {
      name: "name",
      label: "Nama lengkap",
      type: "text",
      required: true,
    },
    {
      name: "email",
      label: "Email",
      type: "email",
      required: true,
    },
    {
      name: "phone",
      label: "No. HP (opsional)",
      type: "text",
      required: false,
    },
    {
      name: "password",
      label: "Password (min. 6 karakter)",
      type: "password",
      required: true,
    },
    {
      name: "password_confirmation",
      label: "Ulangi password",
      type: "password",
      required: true,
    },
  ];

  return (
    <div className="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-5">
      <div className="text-center">
        <h1 className="text-2xl font-extrabold text-[#165823]">
          Daftar akun
        </h1>
        <p className="text-xs text-gray-500 mt-1">
          Gratis dan hanya butuh satu menit.
        </p>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl p-3">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        {fields.map((field) => (
          <div key={field.name}>
            <label className="block text-sm font-bold text-gray-700 mb-1">
              {field.label}
            </label>

            <input
              type={field.type}
              name={field.name}
              value={form[field.name]}
              onChange={handleChange}
              required={field.required}
              minLength={
                field.name === "password" ||
                field.name === "password_confirmation"
                  ? 6
                  : undefined
              }
              className="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none"
            />

            {fieldErrors[field.name] && (
              <p className="text-xs text-red-600 mt-1">
                {fieldErrors[field.name][0]}
              </p>
            )}
          </div>
        ))}

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3 rounded-xl cursor-pointer disabled:opacity-60"
        >
          {loading ? "Memproses..." : "Daftar"}
        </button>
      </form>

      <p className="text-center text-xs text-gray-500">
        Sudah punya akun?{" "}
        <Link
          to="/login"
          className="font-bold text-[#165823] underline"
        >
          Masuk
        </Link>
      </p>
    </div>
  );
}