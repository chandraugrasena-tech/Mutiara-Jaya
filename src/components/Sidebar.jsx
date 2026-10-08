import { Link, useLocation } from "react-router-dom";

export default function Sidebar({ sidebarOpen, setSidebarOpen }) {
  const { pathname } = useLocation();

  const menu = (to, icon, label) => (
    <Link
      to={to}
      onClick={() => setSidebarOpen(false)}
      className={`flex items-center gap-2 p-2 rounded text-sm font-semibold ${
        pathname === to ? "bg-[#165823] text-white" : "text-gray-700 hover:bg-gray-200"
      }`}
    >
      <span>{icon}</span>
      {label}
    </Link>
  );

  return (
    <div
      className={`${
        sidebarOpen ? "block" : "hidden"
      } md:block fixed md:static inset-y-0 left-0 z-40 w-64 bg-white shadow-md`}
    >
      <div className="p-4 font-bold text-xl text-[#165823]">🍲 Admin Mutiara Jaya</div>
      <nav className="flex flex-col p-4 space-y-2">
        {menu("/admin/dashboard", "📊", "Dashboard")}
        {menu("/admin/about", "ℹ️", "About")}
        <Link
          to="/"
          className="flex items-center gap-2 p-2 rounded text-sm font-semibold text-gray-500 hover:bg-gray-200"
        >
          <span>←</span>
          Kembali ke toko
        </Link>
      </nav>
    </div>
  );
}
