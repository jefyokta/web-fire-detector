import { useState, useEffect } from "react";
import { MainPage } from "./MainPage";
import { Check, Loader2, X, Wifi, WifiOff } from "lucide-react";

const Application: React.FC = () => {
  const [wsServer, setWsServer] = useState<string>("");
  const [ws, setWs] = useState<WebSocket | null>(null);
  const [connectionStatus, setConnectionStatus] = useState<"disconnected" | "connecting" | "connected" | "error">("disconnected");


  useEffect(() => {
    const { host, protocol } = window.location
    const protp = protocol == 'http:' ? "ws://" : "wss://"
    const fullHost = `${protp}${host}`
    setWsServer(fullHost)
    const socket = new WebSocket(fullHost)
    socket.onopen = () => {
      setConnectionStatus("connected");
      socket.send(JSON.stringify({ event: "is_fire" }));
    };
    socket.onerror = (error) => {
      console.error("WebSocket error:", error);
      setConnectionStatus("error");
    };
    socket.onclose = () => {
      setConnectionStatus("disconnected");
    };

    setWs(socket)

    return () => ws?.close()
  }, []);



  const disconnect = () => {
    if (ws) {
      ws.close();
      setWs(null);

      setConnectionStatus("disconnected");
    }
  };

  const getStatusColor = () => {
    switch (connectionStatus) {
      case "connected": return "text-emerald-500";
      case "connecting": return "text-amber-500";
      case "error": return "text-rose-500";
      default: return "text-slate-500";
    }
  };

  const getStatusIcon = () => {
    switch (connectionStatus) {
      case "connected": return <Check className="w-4 h-4" />;
      case "connecting": return <Loader2 className="w-4 h-4 animate-spin" />;
      case "error": return <X className="w-4 h-4" />;
      default: return <WifiOff className="w-4 h-4" />;
    }
  };

  return (
    <div className="flex flex-col min-h-screen bg-slate-50">
      <div className="bg-white border-b border-slate-200 px-6 py-3 flex justify-between items-center shadow-sm">
        <h1>
          Rooms Status
        </h1>
        <div className="flex items-center">
          <div className={`flex items-center gap-1.5 ${getStatusColor()}`}>
            {getStatusIcon()}
            <span className="text-sm font-medium">{wsServer}</span>
          </div>
        </div>

      </div>
      <div className="flex-1">
        {ws &&
          <MainPage ws={ws} />
        }
      </div>
    </div>
  );
}

export default Application;