import { Outlet } from "react-router-dom";
import { useState } from "react";
import Sidebar from "../components/Sidebar";

export default function AdminLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="flex h-screen bg-gray-100">
      <Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
      <div className="flex-1 flex flex-col min-w-0">
        <div className="md:hidden bg-white shadow p-4 flex justify-between items-center">
          <h1 className="font-bold text-[#165823]">Admin Mutiara Jaya</h1>
          <button
            className="p-2 border rounded cursor-pointer"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            ☰
          </button>
        </div>

        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>

        <footer className="bg-white border-t p-4 text-center text-sm">
          © 2026 Admin Mutiara Jaya — v1.0.0
        </footer>
      </div>
    </div>
  );
}
