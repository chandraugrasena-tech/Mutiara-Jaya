import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import MainLayout from "./layouts/MainLayout";
import AdminLayout from "./layouts/AdminLayout";
import Dashboard from "./pages/frontpages/Dashboard";
import Cart from "./pages/frontpages/Cart";
import Promo from "./pages/frontpages/Promo";
import ProductDetail from "./pages/frontpages/ProductDetail";
import OrderStatus from "./pages/frontpages/OrderStatus";
import AdminDashboard from "./pages/adminpages/AdminDashboard";
import AboutPage from "./pages/adminpages/AboutPage";
import { CartProvider } from "./utils/CartContext";
import Login from "./pages/frontpages/Login";
import Register from "./pages/frontpages/Register";
import About from "./pages/frontpages/About";
import ProductForm from "./pages/adminpages/ProductForm";
import ProductEdit from "./pages/adminpages/ProductEdit";
import ProductList from "./pages/adminpages/ProductList";

export default function App() {
  return (
    <CartProvider>
      <BrowserRouter>
        <Routes>

          <Route path="/" element={<MainLayout />}>
            <Route index element={<Dashboard />} />
            <Route path="promo" element={<Promo />} />
            <Route path="cart" element={<Cart />} />
            <Route path="product/:id" element={<ProductDetail />} />
            <Route path="order/:id" element={<OrderStatus />} />
            <Route path="login" element={<Login />} />
            <Route path="register" element={<Register />} />
            <Route path="about" element={<About />} />
          </Route>

          <Route path="/admin" element={<AdminLayout />}>
  <Route index element={<Navigate to="dashboard" replace />} />
  <Route path="dashboard" element={<AdminDashboard />} />
  <Route path="products" element={<ProductList />} />
  <Route path="about" element={<AboutPage />} />
  <Route path="add-product" element={<ProductForm />} />
  <Route path="edit-product/:id" element={<ProductEdit />} />
</Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </CartProvider>
  );
}
