import { useMemo } from "react";
import DatePicker from "react-datepicker";
import { getYear, getMonth } from "date-fns";
import "react-datepicker/dist/react-datepicker.css";
import "../../css/datepicker.css";

const formatDob = (date) => {
    if (!date) return '';
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
};

const parseDob = (value) => {
    if (!value) return null;
    const [d, m, y] = value.split('-').map(Number);
    if (!d || !m || !y) return null;
    const parsed = new Date(y, m - 1, d);
    return isNaN(parsed) ? null : parsed;
};

export default function DateOfBirthPicker({
    id = "dob",
    name = "dob",
    value,
    onChange,
    inputClassName = "form-control",
    wrapperClassName = "w-100",
    placeholderText = "dd-mm-yyyy",
    required = false
}) {
    const selected = useMemo(() => parseDob(value), [value]);

    const maxDob = useMemo(() => {
        const date = new Date();
        date.setFullYear(date.getFullYear() - 5);
        return date;
    }, []);

    const dobYears = useMemo(() => {
        const maxYear = maxDob.getFullYear();
        const minYear = maxYear - 100;
        const years = [];
        for (let y = maxYear; y >= minYear; y--) years.push(y);
        return years;
    }, [maxDob]);

    const dobMonths = useMemo(() => [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ], []);

    const handleChange = (date) => {
        const formatted = formatDob(date);
        if (onChange) onChange(formatted);
    };

    const renderHeader = ({
        date,
        changeYear,
        changeMonth,
        decreaseMonth,
        increaseMonth,
        prevMonthButtonDisabled,
        nextMonthButtonDisabled
    }) => (
        <div className="dob-header d-flex align-items-center justify-content-between px-2 py-2 gap-2">
            <button type="button" className="dob-nav btn btn-sm" onClick={decreaseMonth} disabled={prevMonthButtonDisabled}>{"<"}</button>
            <div className="d-flex gap-2">
                <select
                    className="dob-select form-select form-select-sm"
                    value={getYear(date)}
                    onChange={({ target: { value } }) => changeYear(Number(value))}
                >
                    {dobYears.map((option) => (
                        <option key={option} value={option}>{option}</option>
                    ))}
                </select>
                <select
                    className="dob-select form-select form-select-sm"
                    value={getMonth(date)}
                    onChange={({ target: { value } }) => changeMonth(Number(value))}
                >
                    {dobMonths.map((option, index) => (
                        <option key={option} value={index}>{option}</option>
                    ))}
                </select>
            </div>
            <button type="button" className="dob-nav btn btn-sm" onClick={increaseMonth} disabled={nextMonthButtonDisabled}>{">"}</button>
        </div>
    );

    return (
        <DatePicker
            id={id}
            name={name}
            selected={selected}
            className={inputClassName}
            wrapperClassName={wrapperClassName}
            placeholderText={placeholderText}
            dateFormat="dd-MM-yyyy"
            maxDate={maxDob}
            renderCustomHeader={renderHeader}
            onChange={handleChange}
            required={required}
        />
    );
}
