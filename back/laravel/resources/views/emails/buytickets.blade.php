<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmació de Reserva</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1a1a1a;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #f8fafc; border-radius: 10px; overflow: hidden;">
        <!-- Header -->
        <div style="background: #4f46e5; padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">🎬 Cine</h1>
            <p style="color: #e0e7ff; margin: 10px 0 0; font-size: 16px;">El teu cinema de confiança</p>
        </div>

        <!-- Contingut -->
        <div style="padding: 30px;">
            <h2 style="color: #1e293b; margin-top: 0;">Hola {{ $user->name }},</h2>
            <p style="font-size: 16px; line-height: 1.6;">
                Gràcies per confiar en nosaltres per a la teva experiència cinematogràfica.
                A continuació trobaràs els detalls de la teva reserva:
            </p>

            <!-- Detalls Reserva -->
            <div
                style="background: white; border-radius: 8px; padding: 20px; margin: 25px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="color: #4f46e5; margin: 0 0 15px;">📅 Detalls de la sessió</h3>
                <p style="margin: 8px 0;">
                    <strong>Pel·lícula:</strong> {{ $reservation->screening->movie->title }}
                </p>
                <p style="margin: 8px 0;">
                    <strong>Data:</strong> {{ \Carbon\Carbon::parse($reservation->screening->date)->format('d/m/Y') }}
                </p>
                <p style="margin: 8px 0;">
                    <strong>Hora:</strong> {{ $reservation->screening->time }}
                </p>
                <p style="margin: 8px 0;">
                    <strong>Sala:</strong> {{ $reservation->screening->room->name }}
                </p>
            </div>

            <!-- Llistat d'Entrades -->
            <div
                style="background: white; border-radius: 8px; padding: 20px; margin: 25px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="color: #4f46e5; margin: 0 0 15px;">🎫 Les teves entrades</h3>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid #e2e8f0;">Butaca</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 2px solid #e2e8f0;">Tipus</th>
                            <th style="text-align: right; padding: 10px; border-bottom: 2px solid #e2e8f0;">Preu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservation->tickets as $ticket)
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Fila {{ $ticket->seat->row }} -
                                    Seient {{ $ticket->seat->number }}</td>
                                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                    {{ ucfirst($ticket->seat->type) }}</td>
                                <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                    {{ number_format($ticket->price, 2) }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Total:</td>
                            <td style="padding: 10px; text-align: right; font-weight: bold;">
                                {{ number_format($reservation->tickets->sum('price'), 2) }}€</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Botó CTA -->
            <!-- <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/reservations/' . $reservation->id) }}" style="background: #4f46e5; color: white; padding: 12px 25px; 
                          border-radius: 5px; text-decoration: none; display: inline-block;
                          font-weight: 500;">
                    Veure reserva al web
                </a>
            </div> -->
        </div>

        <!-- Footer -->
        <div style="background: #f1f5f9; padding: 20px; text-align: center; font-size: 14px; color: #64748b;">
            <p style="margin: 5px 0;">© {{ date('Y') }} Cine - Tots els drets reservats</p>
            <p style="margin: 5px 0;">Contacte: info@cine.cat</p>
        </div>
    </div>
</body>

</html>