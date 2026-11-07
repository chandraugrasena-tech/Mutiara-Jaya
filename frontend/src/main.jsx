
import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App.jsx";
import "./index.css";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "react-hot-toast";
import { ProductProvider } from "./utils/ProductContext";
import { AuthProvider } from "./utils/AuthContext";


const queryClient = new QueryClient();

ReactDOM.createRoot(document.getElementById("root")).render(
  <React.StrictMode>
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <ProductProvider>
          <App />
          <Toaster position="top-right" reverseOrder={false} />
        </ProductProvider>
      </AuthProvider>
    </QueryClientProvider>
  </React.StrictMode>
);
