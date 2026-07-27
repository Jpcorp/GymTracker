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
  SatisfactionSurvey
} from '../types';

export const INITIAL_CLIENTS: Client[] = [
  {
    id: 'cli-001',
    name: 'Juan Pérez',
    email: 'juan.perez@email.com',
    phone: '+56 9 8765 4321',
    birthDate: '1998-03-15',
    gender: 'male',
    startDate: '2026-05-01',
    heightCm: 180,
    goal: 'Recomposición Corporal & Hipertrofia',
    profilePhoto: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=300',
    status: 'active',
    trainerName: 'Carlos Entrenador',
    notes: 'Priorizar técnica en sentadilla por molestia leve en rodilla izquierda previa.'
  },
  {
    id: 'cli-002',
    name: 'María García',
    email: 'maria.garcia@email.com',
    phone: '+56 9 7654 3210',
    birthDate: '1994-08-22',
    gender: 'female',
    startDate: '2026-05-15',
    heightCm: 165,
    goal: 'Tonificación y Pérdida de Grasa',
    profilePhoto: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=300',
    status: 'active',
    trainerName: 'Carlos Entrenador',
    notes: 'Excelente adherencia al plan de alimentación.'
  },
  {
    id: 'cli-003',
    name: 'Carlos Ruiz',
    email: 'carlos.ruiz@email.com',
    phone: '+56 9 6543 2109',
    birthDate: '1991-11-05',
    gender: 'male',
    startDate: '2026-06-01',
    heightCm: 175,
    goal: 'Aumento de Fuerza y Resistencia',
    profilePhoto: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=300',
    status: 'active',
    trainerName: 'Daniela Entrenadora',
    notes: 'Poco tiempo disponible entre semana, rutina concentrada de 3 días.'
  },
  {
    id: 'cli-004',
    name: 'Ana Torres',
    email: 'ana.torres@email.com',
    phone: '+56 9 5432 1098',
    birthDate: '2000-01-19',
    gender: 'female',
    startDate: '2026-06-20',
    heightCm: 162,
    goal: 'Aumento de Masa Muscular en Glúteos y Piernas',
    profilePhoto: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=300',
    status: 'active',
    trainerName: 'Daniela Entrenadora',
    notes: 'Entrenamiento orientado a hipertrofia tren inferior.'
  },
  {
    id: 'cli-005',
    name: 'Diego Morales',
    email: 'diego.morales@email.com',
    phone: '+56 9 4321 0987',
    birthDate: '1986-07-12',
    gender: 'male',
    startDate: '2026-07-01',
    heightCm: 182,
    goal: 'Acondicionamiento Físico y Salud Articular',
    profilePhoto: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=300',
    status: 'paused',
    trainerName: 'Carlos Entrenador',
    notes: 'Pausado temporalmente por viaje de trabajo (1 semana).'
  }
];

export const INITIAL_INBODY_METRICS: InBodyMetric[] = [
  // Juan Pérez (cli-001) - 4 evaluations every 21 days
  {
    id: 'inbody-001-1',
    clientId: 'cli-001',
    recordedAt: '2026-05-01',
    weightKg: 82.0,
    bodyFatPercentage: 20.2,
    bmi: 25.3,
    metabolicAge: 30,
    basalKcal: 1810,
    visceralFat: 9,
    evaluationId: 'eval-001-1'
  },
  {
    id: 'inbody-001-2',
    clientId: 'cli-001',
    recordedAt: '2026-05-22',
    weightKg: 80.5,
    bodyFatPercentage: 19.3,
    bmi: 24.8,
    metabolicAge: 29,
    basalKcal: 1825,
    visceralFat: 8,
    evaluationId: 'eval-001-2'
  },
  {
    id: 'inbody-001-3',
    clientId: 'cli-001',
    recordedAt: '2026-06-12',
    weightKg: 79.2,
    bodyFatPercentage: 18.5,
    bmi: 24.4,
    metabolicAge: 27,
    basalKcal: 1840,
    visceralFat: 8,
    evaluationId: 'eval-001-3'
  },
  {
    id: 'inbody-001-4',
    clientId: 'cli-001',
    recordedAt: '2026-07-03',
    weightKg: 77.8,
    bodyFatPercentage: 17.2,
    bmi: 24.0,
    metabolicAge: 25,
    basalKcal: 1865,
    visceralFat: 7,
    evaluationId: 'eval-001-4'
  },

  // María García (cli-002)
  {
    id: 'inbody-002-1',
    clientId: 'cli-002',
    recordedAt: '2026-05-15',
    weightKg: 64.5,
    bodyFatPercentage: 28.5,
    bmi: 23.7,
    metabolicAge: 34,
    basalKcal: 1380,
    visceralFat: 6,
    evaluationId: 'eval-002-1'
  },
  {
    id: 'inbody-002-2',
    clientId: 'cli-002',
    recordedAt: '2026-06-05',
    weightKg: 63.1,
    bodyFatPercentage: 27.2,
    bmi: 23.2,
    metabolicAge: 32,
    basalKcal: 1395,
    visceralFat: 5,
    evaluationId: 'eval-002-2'
  },
  {
    id: 'inbody-002-3',
    clientId: 'cli-002',
    recordedAt: '2026-06-26',
    weightKg: 61.8,
    bodyFatPercentage: 25.8,
    bmi: 22.7,
    metabolicAge: 30,
    basalKcal: 1410,
    visceralFat: 5,
    evaluationId: 'eval-002-3'
  }
];

export const INITIAL_BODY_MEASUREMENTS: BodyMeasurement[] = [
  // Juan Pérez
  {
    id: 'meas-001-1',
    clientId: 'cli-001',
    recordedAt: '2026-05-01',
    waistCm: 86.0,
    hipsCm: 99.0,
    chestCm: 101.0,
    rightArmCm: 33.0,
    leftArmCm: 32.5,
    rightThighCm: 56.0,
    leftThighCm: 55.5,
    evaluationId: 'eval-001-1'
  },
  {
    id: 'meas-001-2',
    clientId: 'cli-001',
    recordedAt: '2026-05-22',
    waistCm: 84.5,
    hipsCm: 98.0,
    chestCm: 101.5,
    rightArmCm: 33.5,
    leftArmCm: 33.0,
    rightThighCm: 56.5,
    leftThighCm: 56.0,
    evaluationId: 'eval-001-2'
  },
  {
    id: 'meas-001-3',
    clientId: 'cli-001',
    recordedAt: '2026-06-12',
    waistCm: 82.5,
    hipsCm: 97.0,
    chestCm: 102.0,
    rightArmCm: 34.0,
    leftArmCm: 33.5,
    rightThighCm: 57.0,
    leftThighCm: 56.5,
    evaluationId: 'eval-001-3'
  },
  {
    id: 'meas-001-4',
    clientId: 'cli-001',
    recordedAt: '2026-07-03',
    waistCm: 80.5,
    hipsCm: 96.0,
    chestCm: 103.0,
    rightArmCm: 34.5,
    leftArmCm: 34.0,
    rightThighCm: 58.0,
    leftThighCm: 57.5,
    evaluationId: 'eval-001-4'
  }
];

export const INITIAL_BODY_PHOTOS: BodyPhoto[] = [
  {
    id: 'photo-001-1-front',
    clientId: 'cli-001',
    photoDate: '2026-05-01',
    viewType: 'front',
    photoUrl: 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-1',
    notes: 'Control inicial frente - Mayo'
  },
  {
    id: 'photo-001-1-back',
    clientId: 'cli-001',
    photoDate: '2026-05-01',
    viewType: 'back',
    photoUrl: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-1',
    notes: 'Control inicial espalda - Mayo'
  },
  {
    id: 'photo-001-1-left',
    clientId: 'cli-001',
    photoDate: '2026-05-01',
    viewType: 'left_side',
    photoUrl: 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-1',
    notes: 'Control inicial perfil izquierdo'
  },
  {
    id: 'photo-001-1-right',
    clientId: 'cli-001',
    photoDate: '2026-05-01',
    viewType: 'right_side',
    photoUrl: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-1',
    notes: 'Control inicial perfil derecho'
  },

  // Evaluation #4 photos for Juan
  {
    id: 'photo-001-4-front',
    clientId: 'cli-001',
    photoDate: '2026-07-03',
    viewType: 'front',
    photoUrl: 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-4',
    notes: 'Control 21 días #4 frente - Julio'
  },
  {
    id: 'photo-001-4-back',
    clientId: 'cli-001',
    photoDate: '2026-07-03',
    viewType: 'back',
    photoUrl: 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-4',
    notes: 'Control 21 días #4 espalda - Julio'
  },
  {
    id: 'photo-001-4-left',
    clientId: 'cli-001',
    photoDate: '2026-07-03',
    viewType: 'left_side',
    photoUrl: 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-4',
    notes: 'Control 21 días #4 perfil izq'
  },
  {
    id: 'photo-001-4-right',
    clientId: 'cli-001',
    photoDate: '2026-07-03',
    viewType: 'right_side',
    photoUrl: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=400',
    evaluationId: 'eval-001-4',
    notes: 'Control 21 días #4 perfil der'
  }
];

export const INITIAL_ROUTINES: Routine[] = [
  {
    id: 'routine-001',
    clientId: 'cli-001',
    name: 'Hipertrofia & Fuerza - Bloque 1 (3 Meses)',
    description: 'Rutina estandarizada de 4 días semana dividida en Pecho/Tríceps, Espalda/Bíceps, Pierna Completa, Hombros/Abs.',
    weeklyFrequency: 4,
    startDate: '2026-05-01',
    endDate: '2026-07-31',
    isActive: true,
    exercises: [
      {
        id: 'ex-001',
        routineId: 'routine-001',
        name: 'Press de Banca con Barra',
        muscleGroup: 'Pecho',
        sets: 3,
        repsRange: '15 / 12 / 8-10 (Efectiva)',
        restSeconds: 90,
        notes: 'Serie 1 base de 15 reps. Serie 3 buscar peso máximo efectivo para 8-10 reps.'
      },
      {
        id: 'ex-002',
        routineId: 'routine-001',
        name: 'Sentadilla Libre con Barra',
        muscleGroup: 'Piernas',
        sets: 3,
        repsRange: '15 / 12 / 8-10 (Efectiva)',
        restSeconds: 120,
        notes: 'Cuidar profundidad y rodillas.'
      },
      {
        id: 'ex-003',
        routineId: 'routine-001',
        name: 'Peso Muerto Rumano',
        muscleGroup: 'Isquios & Glúteo',
        sets: 3,
        repsRange: '15 / 12 / 8-10 (Efectiva)',
        restSeconds: 90,
        notes: 'Enfoque en bisagra de cadera.'
      },
      {
        id: 'ex-004',
        routineId: 'routine-001',
        name: 'Remo con Barra',
        muscleGroup: 'Espalda',
        sets: 3,
        repsRange: '15 / 12 / 8-10 (Efectiva)',
        restSeconds: 90,
        notes: 'Tirones hacia el ombligo manteniendo torso estable.'
      },
      {
        id: 'ex-005',
        routineId: 'routine-001',
        name: 'Press Militar con Mancuernas',
        muscleGroup: 'Hombros',
        sets: 3,
        repsRange: '15 / 12 / 8-10 (Efectiva)',
        restSeconds: 90,
        notes: 'Espalda apoyada en banco a 85 grados.'
      }
    ]
  }
];

export const INITIAL_WORKOUT_LOGS: WorkoutLog[] = [
  // Juan Pérez - Bench Press progression across weeks
  {
    id: 'log-001',
    clientId: 'cli-001',
    exerciseId: 'ex-001',
    exerciseName: 'Press de Banca con Barra',
    workoutDate: '2026-05-02',
    baseSetWeightKg: 50,
    effectiveSetWeightKg: 65,
    completedSets: 3,
    completedReps: '15, 12, 10',
    rpe: 8,
    notes: 'Primera sesión base. Sensación fluida.'
  },
  {
    id: 'log-002',
    clientId: 'cli-001',
    exerciseId: 'ex-001',
    exerciseName: 'Press de Banca con Barra',
    workoutDate: '2026-05-23',
    baseSetWeightKg: 55,
    effectiveSetWeightKg: 72.5,
    completedSets: 3,
    completedReps: '15, 12, 9',
    rpe: 8.5,
    notes: 'Aumento de 7.5kg en serie efectiva.'
  },
  {
    id: 'log-003',
    clientId: 'cli-001',
    exerciseId: 'ex-001',
    exerciseName: 'Press de Banca con Barra',
    workoutDate: '2026-06-13',
    baseSetWeightKg: 60,
    effectiveSetWeightKg: 80,
    completedSets: 3,
    completedReps: '15, 12, 8',
    rpe: 9,
    notes: '¡Nuevo récord personal con 80kg!'
  },
  {
    id: 'log-004',
    clientId: 'cli-001',
    exerciseId: 'ex-001',
    exerciseName: 'Press de Banca con Barra',
    workoutDate: '2026-07-04',
    baseSetWeightKg: 62.5,
    effectiveSetWeightKg: 85,
    completedSets: 3,
    completedReps: '15, 12, 8',
    rpe: 9.5,
    notes: '85kg máxima en 3ra serie efectiva.'
  },

  // Sentadilla progression
  {
    id: 'log-005',
    clientId: 'cli-001',
    exerciseId: 'ex-002',
    exerciseName: 'Sentadilla Libre con Barra',
    workoutDate: '2026-05-04',
    baseSetWeightKg: 60,
    effectiveSetWeightKg: 80,
    completedSets: 3,
    completedReps: '15, 12, 10',
    rpe: 8
  },
  {
    id: 'log-006',
    clientId: 'cli-001',
    exerciseId: 'ex-002',
    exerciseName: 'Sentadilla Libre con Barra',
    workoutDate: '2026-07-06',
    baseSetWeightKg: 70,
    effectiveSetWeightKg: 100,
    completedSets: 3,
    completedReps: '15, 12, 8',
    rpe: 9,
    notes: 'Llegó a las 3 cifras en sentadilla con técnica limpia.'
  }
];

export const INITIAL_EVALUATIONS: Evaluation[] = [
  {
    id: 'eval-001-1',
    clientId: 'cli-001',
    evaluationNumber: 1,
    periodStart: '2026-05-01',
    periodEnd: '2026-05-21',
    evaluatedAt: '2026-05-01',
    achievementsSummary: [
      'Evaluación de Diagnóstico Inicial InBody completada',
      'Definición de objetivo: Recomposición Corporal',
      'Asignación de rutina Bloque 1 (3 meses)'
    ],
    trainerNotes: 'Cliente con excelente motivación inicial. Se toma InBody de base.',
    inBodyMetricId: 'inbody-001-1',
    measurementId: 'meas-001-1'
  },
  {
    id: 'eval-001-2',
    clientId: 'cli-001',
    evaluationNumber: 2,
    periodStart: '2026-05-22',
    periodEnd: '2026-06-11',
    evaluatedAt: '2026-05-22',
    achievementsSummary: [
      'Reducción de 1.5 kg de peso corporal',
      'Disminución de 0.9% en grasa corporal',
      'Reducción de 1.5 cm en cintura',
      'Asistencia sobresaliente del 92%'
    ],
    trainerNotes: 'Excelente respuesta en la primera evaluación de 21 días.',
    inBodyMetricId: 'inbody-001-2',
    measurementId: 'meas-001-2'
  },
  {
    id: 'eval-001-3',
    clientId: 'cli-001',
    evaluationNumber: 3,
    periodStart: '2026-06-12',
    periodEnd: '2026-07-02',
    evaluatedAt: '2026-06-12',
    achievementsSummary: [
      'Superación de peso récord en Press de Banca (80 kg)',
      'Aumento de masa muscular sostenido (+15 kcal basal)',
      'Reducción de cintura a 82.5 cm'
    ],
    trainerNotes: 'Progreso constante sin molestias articulares.',
    inBodyMetricId: 'inbody-001-3',
    measurementId: 'meas-001-3'
  },
  {
    id: 'eval-001-4',
    clientId: 'cli-001',
    evaluationNumber: 4,
    periodStart: '2026-07-03',
    periodEnd: '2026-07-23',
    evaluatedAt: '2026-07-03',
    achievementsSummary: [
      'Logro total: -4.2 kg de peso y -3.0% de grasa en 63 días',
      'Press de Banca serie efectiva máxima: 85 kg',
      'Sentadilla serie efectiva máxima: 100 kg',
      'Reducción acumulada de 5.5 cm de cintura'
    ],
    trainerNotes: 'Control de 21 días #4 cumplido con éxito total. Preparando Bloque 2.',
    inBodyMetricId: 'inbody-001-4',
    measurementId: 'meas-001-4'
  }
];

export const INITIAL_ATTENDANCES: Attendance[] = [
  {
    id: 'att-001',
    clientId: 'cli-001',
    attendanceDate: '2026-07-24',
    checkIn: '08:30',
    checkOut: '09:45',
    sessionType: 'personal',
    durationMinutes: 75
  },
  {
    id: 'att-002',
    clientId: 'cli-001',
    attendanceDate: '2026-07-22',
    checkIn: '08:30',
    checkOut: '09:40',
    sessionType: 'personal',
    durationMinutes: 70
  },
  {
    id: 'att-003',
    clientId: 'cli-001',
    attendanceDate: '2026-07-20',
    checkIn: '08:45',
    checkOut: '10:00',
    sessionType: 'personal',
    durationMinutes: 75
  },
  {
    id: 'att-004',
    clientId: 'cli-002',
    attendanceDate: '2026-07-24',
    checkIn: '10:00',
    checkOut: '11:10',
    sessionType: 'personal',
    durationMinutes: 70
  }
];

export const INITIAL_MOOD_RECORDS: MoodRecord[] = [
  {
    id: 'mood-001',
    clientId: 'cli-001',
    weekStart: '2026-07-20',
    weekEnd: '2026-07-26',
    moodLevel: 9,
    energyLevel: 8,
    motivationLevel: 10,
    notes: 'Sintiéndose con gran energía tras superar los 85kg en press de banca.'
  }
];

export const INITIAL_NUTRITION_LOGS: NutritionLog[] = [
  {
    id: 'nut-001',
    clientId: 'cli-001',
    logDate: '2026-07-25',
    compliance: 'complete',
    mealsLogged: 4,
    mealsPlanned: 4,
    notes: 'Cumpimiento al 100% de la pauta de proteínas.'
  }
];

export const INITIAL_SATISFACTION_SURVEYS: SatisfactionSurvey[] = [
  {
    id: 'sat-001',
    clientId: 'cli-001',
    surveyDate: '2026-07-03',
    overallSatisfaction: 10,
    trainerSatisfaction: 10,
    facilitiesSatisfaction: 9,
    routinesSatisfaction: 10,
    comments: 'El seguimiento de cada 21 días con fotos y gráficos InBody me mantiene súper motivado.'
  }
];
