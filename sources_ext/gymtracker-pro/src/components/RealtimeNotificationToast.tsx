import React from 'react';
import { useGym } from '../context/GymContext';
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-react';

export const RealtimeNotificationToast: React.FC = () => {
  const { activeToast, clearToast } = useGym();

  if (!activeToast) return null;

  const icons = {
    success: <CheckCircle2 className="w-5 h-5 text-emerald-400 shrink-0" />,
    alert: <AlertCircle className="w-5 h-5 text-amber-400 shrink-0" />,
    info: <Info className="w-5 h-5 text-cyan-400 shrink-0" />
  };

  const bgStyles = {
    success: 'bg-slate-900 border-emerald-500/40 text-emerald-200',
    alert: 'bg-slate-900 border-amber-500/40 text-amber-200',
    info: 'bg-slate-900 border-cyan-500/40 text-cyan-200'
  };

  return (
    <div className="fixed bottom-5 right-5 z-50 max-w-md w-full px-4 animate-in fade-in slide-in-from-bottom-5 duration-300">
      <div
        className={`p-4 rounded-xl border shadow-2xl flex items-start justify-between gap-3 ${
          bgStyles[activeToast.type || 'info']
        }`}
      >
        <div className="flex items-start gap-3">
          {icons[activeToast.type || 'info']}
          <div>
            <h4 className="font-bold text-sm text-white">{activeToast.title}</h4>
            <p className="text-xs text-slate-300 mt-0.5 leading-relaxed">{activeToast.message}</p>
          </div>
        </div>
        <button
          onClick={clearToast}
          className="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition"
        >
          <X className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
};
