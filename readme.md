# sms-service

Readme.md
Сервис отправки смс через N гейтов. Встраивается в любой messenger broker (основной rabbitmq).
На данный момент следующие гейты:
 - Sms Intel
 - Easy Sms

Возможные настройки смотрите в .env

Проблемы:
 - по ошибке занесли в гейт в глючные — нужная команда очистки по пути `sms.*`
 - либо — комманда вкл-выкл консьюмеры на N часов
 - временно:
 - bin/console sms:enable, sms:disable "+1 hou - к чертям сломался - отключить в настройках, ребутнуть

# plaining

проектирование  - 1h
проект - 2h
отладка - 1h
интегрирование - 1h
переработка transfer - 1/2h
итого: 5.5h

Сервис sms:

алгоритм:
 - записываем в рэдис "sms.current-gate" на 15 минут (проверить работает ли в symfony)
 - если настройки нет — берем первый в списке Registry

Intel:
 arguments: 
  - pass
  - login
  - %env(TIMOUT)

Registry:
 arguments:
  gates:
    easy: Service
    intel: Service
  enabled:
    easy: '%end()'
    intel: ''

 - если первый сломался - отправляем через второй, третий
 - каждый сломанный помечаем "временно сломанным" "sms.[gate].skip": "date" на X минут


EASY_ENABLED=1
INTEL_ENABLED=1
TIMEOUT=5 // таймаут для всех гейтов
SMSID= // имя для смс, чаше всего их нужно согласовывать

// храним список гейтов, отключаем ненужные

class GateRegistry {
    function get() {
	    foreach($gates as $name => $gate) {
	        if (isset($this->enabled[$name]) && !$this->enabled[$name]) {
	           continue;
	        }
	        if ($this->cache("sms.$name.skip")) {
	           continue;
	        }
	        return $gate;
	    }
	    thoows new NoWorkedException("no current gate")
    }

    function disable(string $name, string $disableTo) {
       $this->cache("sms.$name.skip", date(+15));
    }
}

Excecptions:
  TB\Sms\Exceptions\BadNumberException
  TB\Sms\Exceptions\BadMessageException
  TB\Sms\Exceptions\SendException
  TB\Sms\Exceptions\NoWorkedGateException

// тут идет отправка
class Consumer {
   function do() {
      $gate = $this->registry->get();

      foreach($messengers as $m) {
          try {
              $this->nomrolize($phone); // exception
              $this->nomrolizeMessage() // exceiptn
              $status = $gate->send($this->check($m['phone']), $this->check($m['message']));
              if (!$status) { // в том числе нужно отловить таймауты... по хорошему нужно привести эксепшены к одному знаменателю
                 
              }
          } catch SendException, timeout, guzzle {
              return againt; // в итоге сообщение уйдет еще раз через очередь
          } catch BadNumber, BadMessage {
              return reject; // неправильный номер телефона, слишком длинное сообщение?
          } catch NoWorkedGateExeption () {
              // плохо, делаем задержку и проблем чуть позже
              sleep(20);
              return again;
          }
      }
   }
}

Тесты:
  - покрыть тестом консьюмер, отправить сообщение типа `[phone: '', message]`

