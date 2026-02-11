import React from 'react';

export default function QuizCard({ question, onAnswer, selectedAnswer }) {
    const choices = question.answers || [];
    const questionText = question.text || "";

    return (
        <div className="border p-6 rounded-lg shadow-sm bg-white mb-6">
            <h3 className="font-bold text-xl mb-4 text-gray-800">{questionText}</h3>

            <div className="space-y-3">
                {choices.map((choice) => {
                    const choiceId = choice.id;
                    const choiceLabel = choice.text;

                    return (
                        <label key={choiceId} className="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 transition">
                            <input
                                type="radio"
                                name={`q_${question.id}`}
                                checked={Number(selectedAnswer) === Number(choiceId)}
                                onChange={() => onAnswer(choiceId)}
                                className="w-5 h-5 text-blue-600 mr-3"
                            />
                            <span className="text-gray-700">{choiceLabel}</span>
                        </label>
                    );
                })}
            </div>
        </div>
    );
}