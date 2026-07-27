import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import {
  Client,
  InBodyMetric,
  BodyMeasurement,
  BodyPhoto,
  Routine,
  WorkoutLog,
  Attendance,
  Evaluation,
  MoodRecord,
  NutritionLog,
  SatisfactionSurvey,
  PoseType
} from '../types';
import {
  INITIAL_CLIENTS,
  INITIAL_INBODY_METRICS,
  INITIAL_BODY_MEASUREMENTS,
  INITIAL_BODY_PHOTOS,
  INITIAL_ROUTINES,
  INITIAL_WORKOUT_LOGS,
  INITIAL_EVALUATIONS,
  INITIAL_ATTENDANCES,
  INITIAL_MOOD_RECORDS,
  INITIAL_NUTRITION_LOGS,
  INITIAL_SATISFACTION_SURVEYS
} from '../data/initialData';
import { calculateBMI, getNextEvaluationDate } from '../utils/calculations';

interface GymContextType {
  clients: Client[];
  inBodyMetrics: InBodyMetric[];
  bodyMeasurements: BodyMeasurement[];
  bodyPhotos: BodyPhoto[];
  routines: Routine[];
  workoutLogs: WorkoutLog[];
  attendances: Attendance[];
  evaluations: Evaluation[];
  moodRecords: MoodRecord[];
  nutritionLogs: NutritionLog[];
  satisfactionSurveys: SatisfactionSurvey[];
  
  // Realtime notification toast queue
  activeToast: { title: string; message: string; type?: 'info' | 'success' | 'alert' } | null;
  clearToast: () => void;
  showToast: (title: string, message: string, type?: 'info' | 'success' | 'alert') => void;

  // Selected state
  selectedClientId: string | null;
  setSelectedClientId: (id: string | null) => void;

  // Actions
  addClient: (client: Omit<Client, 'id'>) => Client;
  updateClient: (id: string, updates: Partial<Client>) => void;
  deleteClient: (id: string) => void;

  addInBodyMetric: (metric: Omit<InBodyMetric, 'id' | 'bmi'>) => InBodyMetric;
  addBodyMeasurement: (measurement: Omit<BodyMeasurement, 'id'>) => BodyMeasurement;
  addBodyPhoto: (photo: Omit<BodyPhoto, 'id'>) => BodyPhoto;

  addWorkoutLog: (log: Omit<WorkoutLog, 'id'>) => WorkoutLog;
  addAttendance: (attendance: Omit<Attendance, 'id'>) => Attendance;
  generate21DayEvaluation: (clientId: string, trainerNotes?: string) => Evaluation;

  addMoodRecord: (mood: Omit<MoodRecord, 'id'>) => MoodRecord;
  addNutritionLog: (log: Omit<NutritionLog, 'id'>) => NutritionLog;
  addSatisfactionSurvey: (survey: Omit<SatisfactionSurvey, 'id'>) => SatisfactionSurvey;

  // Helpers
  getClientById: (id: string) => Client | undefined;
  getClientInBodyHistory: (clientId: string) => InBodyMetric[];
  getClientMeasurementHistory: (clientId: string) => BodyMeasurement[];
  getClientPhotos: (clientId: string) => BodyPhoto[];
  getClientEvaluations: (clientId: string) => Evaluation[];
  getClientWorkoutLogs: (clientId: string) => WorkoutLog[];
  getClientAttendances: (clientId: string) => Attendance[];
  getClientRoutines: (clientId: string) => Routine[];
  
  // Quick stats
  getLatestInBody: (clientId: string) => InBodyMetric | undefined;
  getLatestMeasurement: (clientId: string) => BodyMeasurement | undefined;
  
  resetToDefaultData: () => void;
}

const GymContext = createContext<GymContextType | undefined>(undefined);

const STORAGE_KEYS = {
  CLIENTS: 'gymtracker_clients_v2',
  INBODY: 'gymtracker_inbody_v2',
  MEASUREMENTS: 'gymtracker_measurements_v2',
  PHOTOS: 'gymtracker_photos_v2',
  ROUTINES: 'gymtracker_routines_v2',
  LOGS: 'gymtracker_logs_v2',
  ATTENDANCE: 'gymtracker_attendance_v2',
  EVALUATIONS: 'gymtracker_evaluations_v2',
  MOOD: 'gymtracker_mood_v2',
  NUTRITION: 'gymtracker_nutrition_v2',
  SURVEYS: 'gymtracker_surveys_v2'
};

export const GymProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [clients, setClients] = useState<Client[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.CLIENTS);
    return saved ? JSON.parse(saved) : INITIAL_CLIENTS;
  });

  const [inBodyMetrics, setInBodyMetrics] = useState<InBodyMetric[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.INBODY);
    return saved ? JSON.parse(saved) : INITIAL_INBODY_METRICS;
  });

  const [bodyMeasurements, setBodyMeasurements] = useState<BodyMeasurement[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.MEASUREMENTS);
    return saved ? JSON.parse(saved) : INITIAL_BODY_MEASUREMENTS;
  });

  const [bodyPhotos, setBodyPhotos] = useState<BodyPhoto[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.PHOTOS);
    return saved ? JSON.parse(saved) : INITIAL_BODY_PHOTOS;
  });

  const [routines, setRoutines] = useState<Routine[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.ROUTINES);
    return saved ? JSON.parse(saved) : INITIAL_ROUTINES;
  });

  const [workoutLogs, setWorkoutLogs] = useState<WorkoutLog[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.LOGS);
    return saved ? JSON.parse(saved) : INITIAL_WORKOUT_LOGS;
  });

  const [attendances, setAttendances] = useState<Attendance[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.ATTENDANCE);
    return saved ? JSON.parse(saved) : INITIAL_ATTENDANCES;
  });

  const [evaluations, setEvaluations] = useState<Evaluation[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.EVALUATIONS);
    return saved ? JSON.parse(saved) : INITIAL_EVALUATIONS;
  });

  const [moodRecords, setMoodRecords] = useState<MoodRecord[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.MOOD);
    return saved ? JSON.parse(saved) : INITIAL_MOOD_RECORDS;
  });

  const [nutritionLogs, setNutritionLogs] = useState<NutritionLog[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.NUTRITION);
    return saved ? JSON.parse(saved) : INITIAL_NUTRITION_LOGS;
  });

  const [satisfactionSurveys, setSatisfactionSurveys] = useState<SatisfactionSurvey[]>(() => {
    const saved = localStorage.getItem(STORAGE_KEYS.SURVEYS);
    return saved ? JSON.parse(saved) : INITIAL_SATISFACTION_SURVEYS;
  });

  const [selectedClientId, setSelectedClientId] = useState<string | null>('cli-001');
  const [activeToast, setActiveToast] = useState<{ title: string; message: string; type?: 'info' | 'success' | 'alert' } | null>(null);

  const showToast = useCallback((title: string, message: string, type: 'info' | 'success' | 'alert' = 'info') => {
    setActiveToast({ title, message, type });
    setTimeout(() => {
      setActiveToast((prev) => (prev?.title === title ? null : prev));
    }, 4500);
  }, []);

  const clearToast = useCallback(() => {
    setActiveToast(null);
  }, []);

  // Save to localStorage
  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.CLIENTS, JSON.stringify(clients));
  }, [clients]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.INBODY, JSON.stringify(inBodyMetrics));
  }, [inBodyMetrics]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.MEASUREMENTS, JSON.stringify(bodyMeasurements));
  }, [bodyMeasurements]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.PHOTOS, JSON.stringify(bodyPhotos));
  }, [bodyPhotos]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.ROUTINES, JSON.stringify(routines));
  }, [routines]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.LOGS, JSON.stringify(workoutLogs));
  }, [workoutLogs]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.ATTENDANCE, JSON.stringify(attendances));
  }, [attendances]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.EVALUATIONS, JSON.stringify(evaluations));
  }, [evaluations]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.MOOD, JSON.stringify(moodRecords));
  }, [moodRecords]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.NUTRITION, JSON.stringify(nutritionLogs));
  }, [nutritionLogs]);

  useEffect(() => {
    localStorage.setItem(STORAGE_KEYS.SURVEYS, JSON.stringify(satisfactionSurveys));
  }, [satisfactionSurveys]);

  // Real-time broadcast sync across tabs
  useEffect(() => {
    const channel = new BroadcastChannel('gymtracker_sync_channel');
    channel.onmessage = (event) => {
      if (event.data?.type === 'GYM_STATE_UPDATE') {
        const { key, payload } = event.data;
        if (key === STORAGE_KEYS.CLIENTS) setClients(payload);
        if (key === STORAGE_KEYS.INBODY) setInBodyMetrics(payload);
        if (key === STORAGE_KEYS.MEASUREMENTS) setBodyMeasurements(payload);
        if (key === STORAGE_KEYS.PHOTOS) setBodyPhotos(payload);
        if (key === STORAGE_KEYS.LOGS) setWorkoutLogs(payload);
        if (key === STORAGE_KEYS.EVALUATIONS) setEvaluations(payload);
        if (key === STORAGE_KEYS.ATTENDANCE) setAttendances(payload);
      }
    };
    return () => channel.close();
  }, []);

  const broadcastChange = (key: string, payload: any) => {
    try {
      const channel = new BroadcastChannel('gymtracker_sync_channel');
      channel.postMessage({ type: 'GYM_STATE_UPDATE', key, payload });
      channel.close();
    } catch (e) {
      // BroadcastChannel fallback
    }
  };

  // Actions
  const addClient = (clientData: Omit<Client, 'id'>): Client => {
    const newId = `cli-${Date.now().toString().slice(-4)}`;
    const newClient: Client = {
      ...clientData,
      id: newId
    };
    const updated = [newClient, ...clients];
    setClients(updated);
    broadcastChange(STORAGE_KEYS.CLIENTS, updated);
    
    // Create initial diagnostic evaluation record
    generate21DayEvaluation(newId, 'Evaluación de Diagnóstico Inicial al ingresar.');
    
    showToast('Cliente Registrado', `Se creó el perfil de ${newClient.name} exitosamente.`, 'success');
    return newClient;
  };

  const updateClient = (id: string, updates: Partial<Client>) => {
    const updated = clients.map((c) => (c.id === id ? { ...c, ...updates } : c));
    setClients(updated);
    broadcastChange(STORAGE_KEYS.CLIENTS, updated);
    showToast('Perfil Actualizado', 'Los cambios en el cliente fueron guardados.', 'info');
  };

  const deleteClient = (id: string) => {
    const client = clients.find(c => c.id === id);
    const updated = clients.filter((c) => c.id !== id);
    setClients(updated);
    broadcastChange(STORAGE_KEYS.CLIENTS, updated);
    if (selectedClientId === id) {
      setSelectedClientId(updated[0]?.id || null);
    }
    showToast('Cliente Eliminado', `El perfil de ${client?.name || id} ha sido removido.`, 'alert');
  };

  const addInBodyMetric = (metricData: Omit<InBodyMetric, 'id' | 'bmi'>): InBodyMetric => {
    const client = clients.find((c) => c.id === metricData.clientId);
    const height = client?.heightCm || 175;
    const bmi = calculateBMI(metricData.weightKg, height);
    
    const newMetric: InBodyMetric = {
      ...metricData,
      id: `inbody-${Date.now().toString().slice(-5)}`,
      bmi
    };

    const updated = [newMetric, ...inBodyMetrics];
    setInBodyMetrics(updated);
    broadcastChange(STORAGE_KEYS.INBODY, updated);
    
    showToast('Métrica InBody Guardada', `Peso: ${newMetric.weightKg}kg | Grasa: ${newMetric.bodyFatPercentage}% | BMI: ${bmi}`, 'success');
    return newMetric;
  };

  const addBodyMeasurement = (measData: Omit<BodyMeasurement, 'id'>): BodyMeasurement => {
    const newMeas: BodyMeasurement = {
      ...measData,
      id: `meas-${Date.now().toString().slice(-5)}`
    };
    const updated = [newMeas, ...bodyMeasurements];
    setBodyMeasurements(updated);
    broadcastChange(STORAGE_KEYS.MEASUREMENTS, updated);
    showToast('Medidas Registradas', 'Se han actualizado las medidas antropométricas.', 'success');
    return newMeas;
  };

  const addBodyPhoto = (photoData: Omit<BodyPhoto, 'id'>): BodyPhoto => {
    const newPhoto: BodyPhoto = {
      ...photoData,
      id: `photo-${Date.now().toString().slice(-5)}`
    };
    const updated = [newPhoto, ...bodyPhotos];
    setBodyPhotos(updated);
    broadcastChange(STORAGE_KEYS.PHOTOS, updated);
    showToast('Foto Añadida', `Fotografía pose (${photoData.viewType}) registrada.`, 'success');
    return newPhoto;
  };

  const addWorkoutLog = (logData: Omit<WorkoutLog, 'id'>): WorkoutLog => {
    const newLog: WorkoutLog = {
      ...logData,
      id: `log-${Date.now().toString().slice(-5)}`
    };
    const updated = [newLog, ...workoutLogs];
    setWorkoutLogs(updated);
    broadcastChange(STORAGE_KEYS.LOGS, updated);
    showToast('Carga Registrada', `${logData.exerciseName}: 1ª Base ${logData.baseSetWeightKg}kg ➔ 3ª Efectiva Max ${logData.effectiveSetWeightKg}kg`, 'success');
    return newLog;
  };

  const addAttendance = (attData: Omit<Attendance, 'id'>): Attendance => {
    const newAtt: Attendance = {
      ...attData,
      id: `att-${Date.now().toString().slice(-5)}`
    };
    const updated = [newAtt, ...attendances];
    setAttendances(updated);
    broadcastChange(STORAGE_KEYS.ATTENDANCE, updated);
    const client = clients.find(c => c.id === attData.clientId);
    showToast('Check-in Registrado', `Asistencia de ${client?.name || 'Cliente'} registrada hoy ${attData.checkIn}.`, 'success');
    return newAtt;
  };

  const generate21DayEvaluation = (clientId: string, trainerNotes?: string): Evaluation => {
    const clientEvals = evaluations.filter((e) => e.clientId === clientId);
    const evalNumber = clientEvals.length + 1;
    
    const clientInBody = inBodyMetrics
      .filter((m) => m.clientId === clientId)
      .sort((a, b) => new Date(b.recordedAt).getTime() - new Date(a.recordedAt).getTime());
      
    const clientMeas = bodyMeasurements
      .filter((m) => m.clientId === clientId)
      .sort((a, b) => new Date(b.recordedAt).getTime() - new Date(a.recordedAt).getTime());

    const client = clients.find((c) => c.id === clientId);
    const startDate = client?.startDate || new Date().toISOString().split('T')[0];
    
    const periodStart = startDate;
    const todayStr = new Date().toISOString().split('T')[0];
    
    // Determine achievements
    const achievements: string[] = [
      `Evaluación Control # ${evalNumber} realizada el ${todayStr}`,
      `Cumplimiento de ciclo de 21 días registrado`
    ];

    if (clientInBody.length >= 2) {
      const latest = clientInBody[0];
      const prev = clientInBody[1];
      const weightDiff = Math.round((latest.weightKg - prev.weightKg) * 10) / 10;
      const fatDiff = Math.round((latest.bodyFatPercentage - prev.bodyFatPercentage) * 10) / 10;
      
      if (weightDiff < 0) achievements.push(`Reducción de peso: ${weightDiff} kg`);
      if (fatDiff < 0) achievements.push(`Disminución de grasa corporal: ${fatDiff}%`);
    }

    const newEval: Evaluation = {
      id: `eval-${Date.now().toString().slice(-5)}`,
      clientId,
      evaluationNumber: evalNumber,
      periodStart,
      periodEnd: todayStr,
      evaluatedAt: todayStr,
      achievementsSummary: achievements,
      trainerNotes: trainerNotes || `Evaluación de control de 21 días #${evalNumber} generada por el sistema.`,
      inBodyMetricId: clientInBody[0]?.id || '',
      measurementId: clientMeas[0]?.id || ''
    };

    const updated = [newEval, ...evaluations];
    setEvaluations(updated);
    broadcastChange(STORAGE_KEYS.EVALUATIONS, updated);
    
    showToast('¡Evaluación de 21 Días Generada!', `Se creó la Evaluación #${evalNumber} para ${client?.name}.`, 'alert');
    return newEval;
  };

  const addMoodRecord = (moodData: Omit<MoodRecord, 'id'>): MoodRecord => {
    const newRecord: MoodRecord = { ...moodData, id: `mood-${Date.now().toString().slice(-5)}` };
    setMoodRecords([newRecord, ...moodRecords]);
    showToast('Bienestar Registrado', `Nivel de ánimo: ${moodData.moodLevel}/10 registrado.`, 'info');
    return newRecord;
  };

  const addNutritionLog = (logData: Omit<NutritionLog, 'id'>): NutritionLog => {
    const newLog: NutritionLog = { ...logData, id: `nut-${Date.now().toString().slice(-5)}` };
    setNutritionLogs([newLog, ...nutritionLogs]);
    showToast('Alimentación Registrada', `Cumplimiento: ${logData.compliance}.`, 'info');
    return newLog;
  };

  const addSatisfactionSurvey = (surveyData: Omit<SatisfactionSurvey, 'id'>): SatisfactionSurvey => {
    const newSurvey: SatisfactionSurvey = { ...surveyData, id: `sat-${Date.now().toString().slice(-5)}` };
    setSatisfactionSurveys([newSurvey, ...satisfactionSurveys]);
    showToast('Encuesta Guardada', '¡Gracias por responder la encuesta de satisfacción!', 'success');
    return newSurvey;
  };

  // Helpers
  const getClientById = (id: string) => clients.find((c) => c.id === id);

  const getClientInBodyHistory = (clientId: string) =>
    inBodyMetrics
      .filter((m) => m.clientId === clientId)
      .sort((a, b) => new Date(a.recordedAt).getTime() - new Date(b.recordedAt).getTime());

  const getClientMeasurementHistory = (clientId: string) =>
    bodyMeasurements
      .filter((m) => m.clientId === clientId)
      .sort((a, b) => new Date(a.recordedAt).getTime() - new Date(b.recordedAt).getTime());

  const getClientPhotos = (clientId: string) =>
    bodyPhotos.filter((p) => p.clientId === clientId);

  const getClientEvaluations = (clientId: string) =>
    evaluations
      .filter((e) => e.clientId === clientId)
      .sort((a, b) => b.evaluationNumber - a.evaluationNumber);

  const getClientWorkoutLogs = (clientId: string) =>
    workoutLogs
      .filter((l) => l.clientId === clientId)
      .sort((a, b) => new Date(b.workoutDate).getTime() - new Date(a.workoutDate).getTime());

  const getClientAttendances = (clientId: string) =>
    attendances
      .filter((a) => a.clientId === clientId)
      .sort((a, b) => new Date(b.attendanceDate).getTime() - new Date(a.attendanceDate).getTime());

  const getClientRoutines = (clientId: string) =>
    routines.filter((r) => r.clientId === clientId);

  const getLatestInBody = (clientId: string) => {
    const history = getClientInBodyHistory(clientId);
    return history[history.length - 1];
  };

  const getLatestMeasurement = (clientId: string) => {
    const history = getClientMeasurementHistory(clientId);
    return history[history.length - 1];
  };

  const resetToDefaultData = () => {
    setClients(INITIAL_CLIENTS);
    setInBodyMetrics(INITIAL_INBODY_METRICS);
    setBodyMeasurements(INITIAL_BODY_MEASUREMENTS);
    setBodyPhotos(INITIAL_BODY_PHOTOS);
    setRoutines(INITIAL_ROUTINES);
    setWorkoutLogs(INITIAL_WORKOUT_LOGS);
    setAttendances(INITIAL_ATTENDANCES);
    setEvaluations(INITIAL_EVALUATIONS);
    setMoodRecords(INITIAL_MOOD_RECORDS);
    setNutritionLogs(INITIAL_NUTRITION_LOGS);
    setSatisfactionSurveys(INITIAL_SATISFACTION_SURVEYS);
    localStorage.clear();
    showToast('Datos Reiniciados', 'Se han cargado los datos iniciales de demostración.', 'info');
  };

  return (
    <GymContext.Provider
      value={{
        clients,
        inBodyMetrics,
        bodyMeasurements,
        bodyPhotos,
        routines,
        workoutLogs,
        attendances,
        evaluations,
        moodRecords,
        nutritionLogs,
        satisfactionSurveys,
        activeToast,
        clearToast,
        showToast,
        selectedClientId,
        setSelectedClientId,
        addClient,
        updateClient,
        deleteClient,
        addInBodyMetric,
        addBodyMeasurement,
        addBodyPhoto,
        addWorkoutLog,
        addAttendance,
        generate21DayEvaluation,
        addMoodRecord,
        addNutritionLog,
        addSatisfactionSurvey,
        getClientById,
        getClientInBodyHistory,
        getClientMeasurementHistory,
        getClientPhotos,
        getClientEvaluations,
        getClientWorkoutLogs,
        getClientAttendances,
        getClientRoutines,
        getLatestInBody,
        getLatestMeasurement,
        resetToDefaultData
      }}
    >
      {children}
    </GymContext.Provider>
  );
};

export const useGym = () => {
  const context = useContext(GymContext);
  if (!context) {
    throw new Error('useGym must be used within a GymProvider');
  }
  return context;
};
