export default function QuizCard({ question, onAnswer }) {
    return (
        <div className="border p-4 rounded mb-4">
            <p className="font-semibold mb-2">{question.question}</p>

            {question.choices.map((choice, index) => (
                <label key={index} className="block">
                    <input
                        type="radio"
                        name={question.id}
                        onChange={() => onAnswer(choice)}
                        className="mr-2"
                    />
                    {choice}
                </label>
            ))}
        </div>
    );
}
